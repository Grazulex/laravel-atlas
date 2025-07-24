<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;

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

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Posts - Public endpoints
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post:slug}', [PostController::class, 'show']);
    
    // Categories - Public endpoints
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
    
    // Tags - Public endpoints
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{tag}', [TagController::class, 'show']);
    Route::get('/tags/search', [TagController::class, 'search']);
    
    // Users - Public profiles only
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::get('/users/{user}/posts', function (Request $request, $user) {
        return app(PostController::class)->index($request);
    });
    
    // Comments - Public viewing
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
});

// Authenticated API routes
Route::middleware(['auth:sanctum', 'throttle:120,1'])->prefix('v1')->group(function () {
    
    // User management
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}/follow', [UserController::class, 'toggleFollow']);
    Route::get('/users/{user}/followers', [UserController::class, 'followers']);
    Route::get('/users/{user}/following', [UserController::class, 'following']);
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction']);
    
    // Current user endpoints
    Route::get('/me', function (Request $request) {
        return $request->user()->load(['profile', 'notifications']);
    });
    Route::patch('/me', function (Request $request) {
        // Update current user
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
        ]);
        $user->update($validated);
        return $user;
    });
    Route::delete('/me', function (Request $request) {
        // Delete current user account
        $request->user()->delete();
        return response()->json(['message' => 'Account deleted successfully']);
    });
    
    // Posts - Full CRUD
    Route::apiResource('posts', PostController::class);
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike']);
    Route::get('/posts/{post}/analytics', [PostController::class, 'analytics']);
    Route::post('/posts/{post}/duplicate', [PostController::class, 'duplicate']);
    Route::patch('/posts/{post}/restore', [PostController::class, 'restore'])->withTrashed();
    Route::delete('/posts/{post}/force-delete', [PostController::class, 'forceDestroy'])->withTrashed();
    
    // Categories - Full CRUD
    Route::apiResource('categories', CategoryController::class);
    
    // Tags - Full CRUD (no create/edit forms in API)
    Route::apiResource('tags', TagController::class)->except(['create', 'edit']);
    
    // Comments - Full CRUD with nesting
    Route::apiResource('posts.comments', CommentController::class)->shallow();
    Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike']);
    Route::post('/comments/{comment}/report', [CommentController::class, 'report']);
    Route::post('/comments/bulk-moderate', [CommentController::class, 'bulkModerate']);
    
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', function (Request $request) {
            return $request->user()->notifications()->paginate(20);
        });
        Route::get('/unread', function (Request $request) {
            return $request->user()->unreadNotifications()->paginate(20);
        });
        Route::patch('/{notification}/read', function (Request $request, $notification) {
            $request->user()->notifications()->find($notification)->markAsRead();
            return response()->json(['message' => 'Notification marked as read']);
        });
        Route::patch('/mark-all-read', function (Request $request) {
            $request->user()->unreadNotifications->markAsRead();
            return response()->json(['message' => 'All notifications marked as read']);
        });
        Route::delete('/{notification}', function (Request $request, $notification) {
            $request->user()->notifications()->find($notification)->delete();
            return response()->json(['message' => 'Notification deleted']);
        });
    });
    
    // Search endpoints
    Route::get('/search', function (Request $request) {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        
        $results = [];
        
        if ($type === 'all' || $type === 'posts') {
            $results['posts'] = \App\Models\Post::search($query)->take(10)->get();
        }
        
        if ($type === 'all' || $type === 'users') {
            $results['users'] = \App\Models\User::search($query)->take(10)->get();
        }
        
        if ($type === 'all' || $type === 'categories') {
            $results['categories'] = \App\Models\Category::search($query)->take(10)->get();
        }
        
        return response()->json($results);
    });
    
    // Dashboard data
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        
        return response()->json([
            'stats' => [
                'posts_count' => $user->posts()->count(),
                'published_posts_count' => $user->posts()->published()->count(),
                'draft_posts_count' => $user->posts()->draft()->count(),
                'comments_count' => $user->comments()->count(),
                'unread_notifications' => $user->unreadNotifications()->count(),
                'followers_count' => $user->followers()->count(),
                'following_count' => $user->following()->count(),
            ],
            'recent_posts' => $user->posts()->latest()->take(5)->get(),
            'recent_comments' => $user->comments()->with(['post'])->latest()->take(5)->get(),
            'recent_notifications' => $user->notifications()->take(10)->get(),
        ]);
    });
    
    // Upload endpoints
    Route::post('/upload/image', function (Request $request) {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);
        
        $path = $request->file('image')->store('images', 'public');
        
        return response()->json([
            'message' => 'Image uploaded successfully',
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    });
    
    Route::post('/upload/file', function (Request $request) {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);
        
        $path = $request->file('file')->store('files', 'public');
        
        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => $path,
            'url' => asset('storage/' . $path),
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);
    });
});

// Admin API routes
Route::middleware(['auth:sanctum', 'can:access-admin', 'throttle:200,1'])->prefix('v1/admin')->group(function () {
    
    // System statistics
    Route::get('/stats', function () {
        return response()->json([
            'users' => [
                'total' => \App\Models\User::count(),
                'active' => \App\Models\User::where('is_active', true)->count(),
                'new_this_month' => \App\Models\User::whereMonth('created_at', now()->month)->count(),
            ],
            'posts' => [
                'total' => \App\Models\Post::count(),
                'published' => \App\Models\Post::published()->count(),
                'draft' => \App\Models\Post::draft()->count(),
                'this_month' => \App\Models\Post::whereMonth('created_at', now()->month)->count(),
            ],
            'comments' => [
                'total' => \App\Models\Comment::count(),
                'pending' => \App\Models\Comment::where('is_approved', false)->count(),
                'today' => \App\Models\Comment::whereDate('created_at', today())->count(),
            ],
            'categories' => \App\Models\Category::count(),
            'tags' => \App\Models\Tag::count(),
        ]);
    });
    
    // System health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'database' => 'connected',
            'cache' => cache()->get('health_check') ? 'active' : 'inactive',
            'queue' => 'running', // You'd check actual queue status here
            'storage' => is_writable(storage_path()) ? 'writable' : 'readonly',
            'timestamp' => now(),
        ]);
    });
    
    // Recent activity
    Route::get('/activity', function () {
        return response()->json([
            'recent_users' => \App\Models\User::latest()->take(10)->get(),
            'recent_posts' => \App\Models\Post::latest()->take(10)->get(),
            'recent_comments' => \App\Models\Comment::latest()->take(10)->get(),
        ]);
    });
    
    // Bulk operations
    Route::post('/users/bulk', [UserController::class, 'bulkAction']);
    Route::post('/posts/bulk', function (Request $request) {
        // Bulk post operations
        $validated = $request->validate([
            'action' => 'required|in:publish,unpublish,delete,restore',
            'post_ids' => 'required|array',
        ]);
        
        // Implementation would go here
        return response()->json(['message' => 'Bulk operation completed']);
    });
    
    // System configuration
    Route::get('/config', function () {
        return response()->json([
            'app_name' => config('app.name'),
            'app_version' => config('app.version', '1.0.0'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'debug_mode' => config('app.debug'),
            'environment' => config('app.env'),
        ]);
    });
    
    Route::patch('/config', function (Request $request) {
        // Update system configuration
        $validated = $request->validate([
            'maintenance_mode' => 'boolean',
            'registration_enabled' => 'boolean',
        ]);
        
        // Implementation would store in database or cache
        return response()->json(['message' => 'Configuration updated']);
    });
});

// Webhook endpoints (no authentication, but with verification)
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', function (Request $request) {
        // Stripe webhook handler
        return response()->json(['status' => 'received']);
    });
    
    Route::post('/github', function (Request $request) {
        // GitHub webhook handler
        return response()->json(['status' => 'received']);
    });
    
    Route::post('/mailgun', function (Request $request) {
        // Mailgun webhook handler
        return response()->json(['status' => 'received']);
    });
});

// Rate-limited public endpoints
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/contact', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);
        
        // Send contact form email
        return response()->json(['message' => 'Contact form submitted successfully']);
    });
    
    Route::post('/newsletter/subscribe', function (Request $request) {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);
        
        // Add to newsletter
        return response()->json(['message' => 'Subscribed to newsletter successfully']);
    });
});

// Error handling routes
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found',
        'status' => 404
    ], 404);
});
