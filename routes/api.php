<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/me', [AuthController::class, 'me']);

// Forum routes
Route::prefix('forum')->group(function () {
    Route::get('/forums', [ForumController::class, 'getForums']);
    Route::get('/forums/{id}', [ForumController::class, 'getForumDetails']);
    Route::post('/forums', [ForumController::class, 'createForum']);
    Route::post('/forums/join', [ForumController::class, 'joinForum']);
    
    Route::get('/posts', [ForumController::class, 'getPosts']);
    Route::post('/posts', [ForumController::class, 'createPost']);
    
    Route::get('/comments', [ForumController::class, 'getComments']);
    Route::post('/comments', [ForumController::class, 'createComment']);
});

// Messaging routes
Route::prefix('messaging')->group(function () {
    Route::get('/conversations', [MessagingController::class, 'getConversations']);
    Route::get('/messages', [MessagingController::class, 'getMessages']);
    Route::post('/messages', [MessagingController::class, 'sendMessage']);
    Route::post('/conversations/group', [MessagingController::class, 'createGroupChat']);
});

// Notification routes
Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'getNotifications']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
});

// Upload routes
Route::post('/upload', [UploadController::class, 'upload']);

// Legacy route support (for backward compatibility with old API endpoints)
Route::any('/forum_endpoints.php', function (Request $request) {
    $action = $request->input('action');
    $controller = app(ForumController::class);
    
    // Map old action-based routes to controller methods
    switch ($action) {
        case 'create_forum':
            return $controller->createForum($request);
        case 'get_forums':
            return $controller->getForums($request);
        case 'get_forum_details':
            return $controller->getForumDetails($request, $request->input('forum_id'));
        case 'create_post':
            return $controller->createPost($request);
        case 'get_posts':
            return $controller->getPosts($request);
        case 'create_comment':
            return $controller->createComment($request);
        case 'get_comments':
            return $controller->getComments($request);
        case 'join_forum':
            return $controller->joinForum($request);
        default:
            return response()->json(['status' => 404, 'message' => 'Action not found'], 404);
    }
});

Route::any('/messaging_endpoints.php', function (Request $request) {
    $action = $request->input('action');
    $controller = app(MessagingController::class);
    
    switch ($action) {
        case 'get_conversations':
            return $controller->getConversations($request);
        case 'get_messages':
            return $controller->getMessages($request);
        case 'send_message':
            return $controller->sendMessage($request);
        case 'create_group_chat':
            return $controller->createGroupChat($request);
        default:
            return response()->json(['status' => 404, 'message' => 'Action not found'], 404);
    }
});

Route::any('/notification_endpoints.php', function (Request $request) {
    $action = $request->input('action');
    $controller = app(NotificationController::class);
    
    switch ($action) {
        case 'get_notifications':
            return $controller->getNotifications($request);
        default:
            return response()->json(['status' => 404, 'message' => 'Action not found'], 404);
    }
});

Route::any('/upload_endpoint.php', [UploadController::class, 'upload']);
Route::any('/auth/login.php', [AuthController::class, 'login']);
Route::any('/auth/register.php', [AuthController::class, 'register']);

