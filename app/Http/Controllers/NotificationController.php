<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get user ID from session
     */
    private function getUserId()
    {
        return session('user_id');
    }

    /**
     * Get notifications
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $type = $request->input('type');
            $unreadOnly = $request->input('unread_only', false);
            $page = max(1, intval($request->input('page', 1)));
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $query = DB::table('notifications')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc');

            if ($type) {
                $query->where('type', $type);
            }

            if ($unreadOnly) {
                $query->where('is_read', false);
            }

            $notifications = $query->offset($offset)->limit($limit)->get();

            $unreadCount = DB::table('notifications')
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'status' => 200,
                'data' => [
                    'notifications' => $notifications,
                    'unread_count' => $unreadCount,
                    'page' => $page,
                    'has_more' => count($notifications) === $limit
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("Get notifications error: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            DB::table('notifications')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'status' => 200,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            \Log::error("Mark notification as read error: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            DB::table('notifications')
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'status' => 200,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            \Log::error("Mark all notifications as read error: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Server error'
            ], 500);
        }
    }
}

