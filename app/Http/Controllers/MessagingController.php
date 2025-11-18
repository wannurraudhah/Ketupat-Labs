<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessagingController extends Controller
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
     * Get conversations
     */
    public function getConversations(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(200, ['conversations' => []]);
        }

        try {
            $sort = $request->input('sort', 'recent');
            $order = $sort === 'oldest' ? 'ASC' : 'DESC';

            $conversations = DB::table('conversations as c')
                ->join('conversation_participants as cp', 'c.id', '=', 'cp.conversation_id')
                ->where('cp.user_id', $userId)
                ->select('c.id', 'c.name', 'c.type', 'c.updated_at')
                ->orderBy('c.updated_at', $order)
                ->distinct()
                ->get();

            // Get participant info for each conversation
            foreach ($conversations as $conv) {
                if ($conv->type === 'direct') {
                    // Get other participant
                    $otherUser = DB::table('conversation_participants as cp')
                        ->join('users as u', 'cp.user_id', '=', 'u.id')
                        ->where('cp.conversation_id', $conv->id)
                        ->where('cp.user_id', '!=', $userId)
                        ->select('u.id', 'u.username', 'u.full_name', 'u.avatar_url', 'u.is_online')
                        ->first();
                    $conv->other_user = $otherUser;
                } else {
                    // Get all participants for group
                    $participants = DB::table('conversation_participants as cp')
                        ->join('users as u', 'cp.user_id', '=', 'u.id')
                        ->where('cp.conversation_id', $conv->id)
                        ->select('u.id', 'u.username', 'u.full_name', 'u.avatar_url', 'u.is_online')
                        ->get();
                    $conv->participants = $participants;
                }

                // Get last message
                $lastMessage = DB::table('messages')
                    ->where('conversation_id', $conv->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $conv->last_message = $lastMessage;

                // Get unread count
                $unreadCount = DB::table('messages')
                    ->where('conversation_id', $conv->id)
                    ->where('sender_id', '!=', $userId)
                    ->whereNotIn('id', function($query) use ($userId, $conv) {
                        $query->select('message_id')
                              ->from('message_reads')
                              ->where('user_id', $userId)
                              ->where('conversation_id', $conv->id);
                    })
                    ->count();
                $conv->unread_count = $unreadCount;
            }

            return $this->sendResponse(200, ['conversations' => $conversations]);
        } catch (\Exception $e) {
            \Log::error("Get conversations error: " . $e->getMessage());
            return $this->sendResponse(200, ['conversations' => []]);
        }
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(400, null, 'conversation_id required');
        }

        try {
            $conversationId = $request->input('conversation_id');

            // Check if user is participant
            $isParticipant = DB::table('conversation_participants')
                ->where('conversation_id', $conversationId)
                ->where('user_id', $userId)
                ->exists();

            if (!$isParticipant) {
                return $this->sendResponse(403, null, 'Not a participant in this conversation');
            }

            $messages = DB::table('messages as m')
                ->join('users as u', 'm.sender_id', '=', 'u.id')
                ->where('m.conversation_id', $conversationId)
                ->select(
                    'm.id',
                    'm.sender_id',
                    'm.content',
                    'm.message_type',
                    'm.attachment_url',
                    'm.attachment_name',
                    'm.attachment_size',
                    'm.created_at',
                    'u.username as sender_username',
                    'u.full_name as sender_name',
                    'u.avatar_url as sender_avatar'
                )
                ->orderBy('m.created_at', 'asc')
                ->get();

            // Mark messages as read
            $messageIds = $messages->pluck('id')->toArray();
            foreach ($messageIds as $messageId) {
                DB::table('message_reads')->insertOrIgnore([
                    'user_id' => $userId,
                    'conversation_id' => $conversationId,
                    'message_id' => $messageId,
                    'read_at' => now(),
                ]);
            }

            return $this->sendResponse(200, ['messages' => $messages]);
        } catch (\Exception $e) {
            \Log::error("Get messages error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer',
            'content' => 'required|string',
            'message_type' => 'nullable|in:text,file,image',
            'attachment_url' => 'nullable|string',
            'attachment_name' => 'nullable|string',
            'attachment_size' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(400, null, 'Validation failed');
        }

        try {
            $conversationId = $request->input('conversation_id');

            // Check if user is participant
            $isParticipant = DB::table('conversation_participants')
                ->where('conversation_id', $conversationId)
                ->where('user_id', $userId)
                ->exists();

            if (!$isParticipant) {
                return $this->sendResponse(403, null, 'Not a participant in this conversation');
            }

            $messageId = DB::table('messages')->insertGetId([
                'conversation_id' => $conversationId,
                'sender_id' => $userId,
                'content' => $request->input('content'),
                'message_type' => $request->input('message_type', 'text'),
                'attachment_url' => $request->input('attachment_url'),
                'attachment_name' => $request->input('attachment_name'),
                'attachment_size' => $request->input('attachment_size'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update conversation updated_at
            DB::table('conversations')
                ->where('id', $conversationId)
                ->update(['updated_at' => now()]);

            // Create notifications for other participants
            $otherParticipants = DB::table('conversation_participants')
                ->where('conversation_id', $conversationId)
                ->where('user_id', '!=', $userId)
                ->pluck('user_id')
                ->toArray();

            $user = DB::table('users')->where('id', $userId)->first();
            $senderName = $user->username ?? 'Guest';

            foreach ($otherParticipants as $participantId) {
                DB::table('notifications')->insert([
                    'user_id' => $participantId,
                    'type' => 'message',
                    'title' => "New message from {$senderName}",
                    'message' => $request->input('content'),
                    'related_type' => 'message',
                    'related_id' => $messageId,
                    'created_at' => now(),
                ]);
            }

            return $this->sendResponse(200, ['message_id' => $messageId], 'Message sent successfully');
        } catch (\Exception $e) {
            \Log::error("Send message error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }

    /**
     * Create group chat
     */
    public function createGroupChat(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return $this->sendResponse(401, null, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'members' => 'required|array|min:1',
            'members.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(400, null, 'Validation failed');
        }

        try {
            DB::beginTransaction();

            $conversationId = DB::table('conversations')->insertGetId([
                'name' => $request->input('name'),
                'type' => 'group',
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add creator as participant
            DB::table('conversation_participants')->insert([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'created_at' => now(),
            ]);

            // Add other members
            $members = $request->input('members');
            foreach ($members as $memberId) {
                if ($memberId != $userId) {
                    DB::table('conversation_participants')->insert([
                        'conversation_id' => $conversationId,
                        'user_id' => $memberId,
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return $this->sendResponse(200, ['conversation_id' => $conversationId], 'Group chat created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Create group chat error: " . $e->getMessage());
            return $this->sendResponse(500, null, 'Server error');
        }
    }
}

