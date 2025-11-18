<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    /**
     * Get user ID from session
     */
    private function getUserId()
    {
        return session('user_id');
    }

    /**
     * Send JSON response
     */
    private function sendResponse($status, $data = null, $message = '')
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Create a new forum
     */
    public function createForum(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'visibility' => 'nullable|in:public,private,class',
            'class_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(400, null, 'Validation failed');
        }

        try {
            DB::beginTransaction();

            $forumId = DB::table('forums')->insertGetId([
                'created_by' => $userId,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'category' => $request->input('category'),
                'visibility' => $request->input('visibility', 'public'),
                'class_id' => $request->input('class_id'),
                'member_count' => 1,
                'post_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add tags
            if ($request->has('tags')) {
                foreach ($request->input('tags') as $tag) {
                    if (strlen($tag) <= 50) {
                        DB::table('forum_tags')->insert([
                            'forum_id' => $forumId,
                            'tag_name' => $tag,
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            // Add creator as admin
            DB::table('forum_members')->insert([
                'forum_id' => $forumId,
                'user_id' => $userId,
                'role' => 'admin',
                'created_at' => now(),
            ]);

            DB::commit();

            return $this->sendResponse(200, ['forum_id' => $forumId], 'Forum created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Create forum error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Get forums
     */
    public function getForums(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        try {
            $query = DB::table('forums as f')
                ->leftJoin('forum_statistics as fs', 'f.id', '=', 'fs.forum_id')
                ->leftJoin('users as u', 'f.created_by', '=', 'u.id')
                ->join('forum_members as fm', function($join) use ($userId) {
                    $join->on('f.id', '=', 'fm.forum_id')
                         ->where('fm.user_id', '=', $userId);
                })
                ->where('f.status', 'active')
                ->select(
                    'f.id',
                    'f.title',
                    'f.description',
                    'f.category',
                    'f.visibility',
                    'f.status',
                    'f.is_pinned',
                    'f.member_count',
                    'f.post_count',
                    'f.created_at',
                    'fs.last_activity',
                    'u.username as creator_username',
                    'u.full_name as creator_name'
                );

            // Apply filters
            if ($request->has('category')) {
                $query->where('f.category', $request->input('category'));
            }

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('f.title', 'like', "%{$search}%")
                      ->orWhere('f.description', 'like', "%{$search}%");
                });
            }

            // Sort
            $sort = $request->input('sort', 'recent');
            if ($sort === 'popular') {
                $query->orderBy('f.post_count', 'desc')
                      ->orderBy('f.member_count', 'desc');
            } else {
                $query->orderBy('f.is_pinned', 'desc')
                      ->orderBy('fs.last_activity', 'desc')
                      ->orderBy('f.created_at', 'desc');
            }

            $forums = $query->get();

            // Get tags for each forum
            foreach ($forums as $forum) {
                $tags = DB::table('forum_tags')
                    ->where('forum_id', $forum->id)
                    ->pluck('tag_name')
                    ->toArray();
                $forum->tags = $tags;
            }

            return $this->sendResponse(200, ['forums' => $forums]);
        } catch (\Exception $e) {
            \Log::error("Get forums error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Get forum details
     */
    public function getForumDetails(Request $request, $id): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        try {
            $forum = DB::table('forums as f')
                ->leftJoin('forum_statistics as fs', 'f.id', '=', 'fs.forum_id')
                ->leftJoin('users as u', 'f.created_by', '=', 'u.id')
                ->leftJoin('forum_members as fm', function($join) use ($userId) {
                    $join->on('f.id', '=', 'fm.forum_id')
                         ->where('fm.user_id', '=', $userId);
                })
                ->where('f.id', $id)
                ->select(
                    'f.*',
                    'fs.last_activity',
                    'u.username as creator_username',
                    'u.full_name as creator_name',
                    'fm.id as is_member',
                    'fm.role as user_role',
                    'fm.is_favorite'
                )
                ->first();

            if (!$forum) {
                return $this->sendResponse(404, null, 'Forum not found');
            }

            $forum->is_member = !empty($forum->is_member);
            $forum->is_favorite = !empty($forum->is_favorite);

            // Get tags
            $tags = DB::table('forum_tags')
                ->where('forum_id', $id)
                ->pluck('tag_name')
                ->toArray();
            $forum->tags = $tags;

            return $this->sendResponse(200, ['forum' => $forum]);
        } catch (\Exception $e) {
            \Log::error("Get forum details error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Create a post
     */
    public function createPost(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'forum_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(400, null, 'Validation failed');
        }

        try {
            // Check if user is member
            $isMember = DB::table('forum_members')
                ->where('forum_id', $request->input('forum_id'))
                ->where('user_id', $userId)
                ->exists();

            if (!$isMember) {
                return $this->sendResponse(403, null, 'Not a member of this forum');
            }

            DB::beginTransaction();

            $postId = DB::table('forum_posts')->insertGetId([
                'forum_id' => $request->input('forum_id'),
                'author_id' => $userId,
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'category' => $request->input('category'),
                'post_type' => $request->input('post_type', 'post'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add tags
            if ($request->has('tags')) {
                foreach ($request->input('tags') as $tag) {
                    DB::table('post_tags')->insert([
                        'post_id' => $postId,
                        'tag_name' => $tag,
                        'created_at' => now(),
                    ]);
                }
            }

            // Add attachments
            if ($request->has('attachments')) {
                foreach ($request->input('attachments') as $attachment) {
                    DB::table('post_attachments')->insert([
                        'post_id' => $postId,
                        'file_url' => $attachment['url'] ?? '',
                        'file_name' => $attachment['name'] ?? '',
                        'file_type' => $attachment['type'] ?? '',
                        'file_size' => $attachment['size'] ?? 0,
                        'created_at' => now(),
                    ]);
                }
            }

            // Update forum post count
            DB::table('forums')
                ->where('id', $request->input('forum_id'))
                ->increment('post_count');

            DB::commit();

            return $this->sendResponse(200, ['post_id' => $postId], 'Post created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Create post error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Get posts
     */
    public function getPosts(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        try {
            $query = DB::table('forum_posts as fp')
                ->join('users as u', 'fp.author_id', '=', 'u.id')
                ->join('forums as f', 'fp.forum_id', '=', 'f.id')
                ->where('fp.is_deleted', false)
                ->select(
                    'fp.id',
                    'fp.forum_id',
                    'fp.author_id',
                    'fp.title',
                    'fp.content',
                    'fp.category',
                    'fp.is_pinned',
                    'fp.view_count',
                    'fp.reply_count',
                    'fp.reaction_count',
                    'fp.is_edited',
                    'fp.edited_at',
                    'fp.created_at',
                    'f.title as forum_name',
                    'u.username as author_username',
                    'u.full_name as author_name',
                    'u.avatar_url as author_avatar'
                );

            if ($request->has('forum_id')) {
                $query->where('fp.forum_id', $request->input('forum_id'));
            }

            if ($request->has('post_id')) {
                $query->where('fp.id', $request->input('post_id'));
            }

            $query->orderBy('fp.is_pinned', 'desc')
                  ->orderBy('fp.created_at', 'desc');

            $page = $request->input('page', 1);
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $posts = $query->offset($offset)->limit($limit)->get();

            // Get attachments and tags for each post
            foreach ($posts as $post) {
                $attachments = DB::table('post_attachments')
                    ->where('post_id', $post->id)
                    ->get()
                    ->map(function($att) {
                        return [
                            'id' => $att->id,
                            'url' => $att->file_url,
                            'name' => $att->file_name,
                            'type' => $att->file_type,
                            'size' => $att->file_size,
                        ];
                    })
                    ->toArray();
                $post->attachments = $attachments;

                $tags = DB::table('post_tags')
                    ->where('post_id', $post->id)
                    ->pluck('tag_name')
                    ->toArray();
                $post->tags = $tags;

                // Check if user bookmarked
                $post->is_bookmarked = DB::table('saved_posts')
                    ->where('post_id', $post->id)
                    ->where('user_id', $userId)
                    ->exists();

                // Check if user reacted
                $post->user_reacted = DB::table('reactions')
                    ->where('target_type', 'post')
                    ->where('target_id', $post->id)
                    ->where('user_id', $userId)
                    ->exists();
            }

            return $this->sendResponse(200, [
                'posts' => $posts,
                'page' => $page,
                'has_more' => count($posts) === $limit
            ]);
        } catch (\Exception $e) {
            \Log::error("Get posts error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Create a comment
     */
    public function createComment(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'content' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(400, null, 'Comment content is required');
        }

        try {
            DB::beginTransaction();

            $commentId = DB::table('comments')->insertGetId([
                'post_id' => $request->input('post_id'),
                'author_id' => $userId,
                'parent_id' => $request->input('parent_id'),
                'content' => $request->input('content'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update post reply count
            DB::table('forum_posts')
                ->where('id', $request->input('post_id'))
                ->increment('reply_count');

            DB::commit();

            return $this->sendResponse(200, ['comment_id' => $commentId], 'Comment created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Create comment error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Get comments
     */
    public function getComments(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        try {
            $postId = $request->input('post_id');
            if (!$postId) {
                return $this->sendResponse(400, null, 'post_id required');
            }

            // Get top-level comments
            $comments = DB::table('comments as c')
                ->join('users as u', 'c.author_id', '=', 'u.id')
                ->where('c.post_id', $postId)
                ->where('c.is_deleted', false)
                ->whereNull('c.parent_id')
                ->select(
                    'c.id',
                    'c.parent_id',
                    'c.content',
                    'c.is_edited',
                    'c.edited_at',
                    'c.reaction_count',
                    'c.created_at',
                    'u.username as author_username',
                    'u.full_name as author_name',
                    'u.avatar_url as author_avatar'
                )
                ->orderBy('c.created_at', 'desc')
                ->get();

            // Recursively load replies (simplified - can be optimized)
            foreach ($comments as $comment) {
                $comment->replies = $this->getCommentReplies($comment->id, $userId);
                $comment->reply_count = count($comment->replies);
                $comment->user_reacted = DB::table('reactions')
                    ->where('target_type', 'comment')
                    ->where('target_id', $comment->id)
                    ->where('user_id', $userId)
                    ->exists();
            }

            return $this->sendResponse(200, ['comments' => $comments]);
        } catch (\Exception $e) {
            \Log::error("Get comments error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Get comment replies recursively
     */
    private function getCommentReplies($parentId, $userId)
    {
        $replies = DB::table('comments as c')
            ->join('users as u', 'c.author_id', '=', 'u.id')
            ->where('c.parent_id', $parentId)
            ->where('c.is_deleted', false)
            ->select(
                'c.id',
                'c.parent_id',
                'c.content',
                'c.is_edited',
                'c.edited_at',
                'c.reaction_count',
                'c.created_at',
                'u.username as author_username',
                'u.full_name as author_name',
                'u.avatar_url as author_avatar'
            )
            ->orderBy('c.created_at', 'desc')
            ->get();

        foreach ($replies as $reply) {
            $reply->replies = $this->getCommentReplies($reply->id, $userId);
            $reply->reply_count = count($reply->replies);
            $reply->user_reacted = DB::table('reactions')
                ->where('target_type', 'comment')
                ->where('target_id', $reply->id)
                ->where('user_id', $userId)
                ->exists();
        }

        return $replies;
    }

    /**
     * Join forum
     */
    public function joinForum(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'forum_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(400, null, 'forum_id required');
        }

        try {
            $forumId = $request->input('forum_id');

            // Check if already a member
            if (DB::table('forum_members')->where('forum_id', $forumId)->where('user_id', $userId)->exists()) {
                return $this->sendResponse(400, null, 'Already a member of this forum');
            }

            DB::beginTransaction();

            DB::table('forum_members')->insert([
                'forum_id' => $forumId,
                'user_id' => $userId,
                'role' => 'member',
                'created_at' => now(),
            ]);

            DB::table('forums')->where('id', $forumId)->increment('member_count');

            DB::commit();

            return $this->sendResponse(200, null, 'Joined forum successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Join forum error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }
}

