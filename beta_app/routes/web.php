<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes (handled by Laravel Breeze/Jetstream/Sanctum)
// Auth::routes(); // Uncomment if using Laravel UI

// Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
});

// Posts Routes - Full RESTful resource with additional custom routes
Route::resource('posts', PostController::class)->parameters(['posts' => 'post:slug']);

// Additional post routes
Route::middleware('auth')->group(function () {
    // Post management
    Route::patch('/posts/{post}/restore', [PostController::class, 'restore'])
        ->name('posts.restore')
        ->withTrashed();
    Route::delete('/posts/{post}/force-delete', [PostController::class, 'forceDestroy'])
        ->name('posts.force-destroy')
        ->withTrashed();
    
    // Post interactions
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])
        ->name('posts.like');
    Route::post('/posts/{post}/duplicate', [PostController::class, 'duplicate'])
        ->name('posts.duplicate');
    
    // Post analytics (for authors and admins)
    Route::get('/posts/{post}/analytics', [PostController::class, 'analytics'])
        ->name('posts.analytics');
});

// Users Routes - Full RESTful resource with additional routes
Route::resource('users', UserController::class)->middleware('auth');

// Additional user routes
Route::middleware('auth')->group(function () {
    // User following system
    Route::post('/users/{user}/follow', [UserController::class, 'toggleFollow'])
        ->name('users.follow');
    Route::get('/users/{user}/followers', [UserController::class, 'followers'])
        ->name('users.followers');
    Route::get('/users/{user}/following', [UserController::class, 'following'])
        ->name('users.following');
    
    // Bulk user actions (admin only)
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])
        ->name('users.bulk-action')
        ->middleware('can:bulkAction,App\Models\User');
});

// Public user profiles
Route::get('/profile/{user:username}', [UserController::class, 'profile'])
    ->name('users.public-profile');

// Categories Routes - Full RESTful resource
Route::resource('categories', CategoryController::class)->parameters(['categories' => 'category:slug']);

// Tags Routes - RESTful with search
Route::resource('tags', TagController::class)->except(['create', 'edit']);

// Tag search for autocomplete
Route::get('/tags/search', [TagController::class, 'search'])
    ->name('tags.search');

// Comments Routes - Nested under posts
Route::prefix('posts/{post}')->name('posts.')->group(function () {
    Route::resource('comments', CommentController::class)->except(['create', 'edit']);
    
    // Comment interactions
    Route::middleware('auth')->group(function () {
        Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike'])
            ->name('comments.like');
        Route::post('/comments/{comment}/report', [CommentController::class, 'report'])
            ->name('comments.report');
    });
});

// Standalone comment routes for moderation
Route::middleware('auth')->group(function () {
    Route::get('/comments/{comment}', [CommentController::class, 'show'])
        ->name('comments.show');
    Route::post('/comments/bulk-moderate', [CommentController::class, 'bulkModerate'])
        ->name('comments.bulk-moderate')
        ->middleware('can:moderate,App\Models\Comment');
});

// API Routes (JSON responses)
Route::prefix('api/v1')->name('api.')->middleware('throttle:60,1')->group(function () {
    // Public API endpoints
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post:slug}', [PostController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{tag}', [TagController::class, 'show']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    
    // Authenticated API endpoints
    Route::middleware('auth:sanctum')->group(function () {
        // User management
        Route::apiResource('users', UserController::class);
        Route::post('/users/{user}/follow', [UserController::class, 'toggleFollow']);
        
        // Content management
        Route::apiResource('posts', PostController::class);
        Route::post('/posts/{post}/like', [PostController::class, 'toggleLike']);
        Route::get('/posts/{post}/analytics', [PostController::class, 'analytics']);
        
        // Categories and tags
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('tags', TagController::class);
        
        // Comments
        Route::apiResource('posts.comments', CommentController::class);
        Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike']);
        Route::post('/comments/{comment}/report', [CommentController::class, 'report']);
    });
});

// Admin routes (protected by admin middleware)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:access-admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Admin-specific resource management with additional HTTP methods
    Route::get('/posts/trashed', function () {
        return view('admin.posts.trashed');
    })->name('posts.trashed');
    
    Route::get('/users/inactive', function () {
        return view('admin.users.inactive');
    })->name('users.inactive');
    
    // Bulk operations
    Route::patch('/posts/bulk-restore', function () {
        // Bulk restore logic
    })->name('posts.bulk-restore');
    
    Route::delete('/posts/bulk-delete', function () {
        // Bulk delete logic  
    })->name('posts.bulk-delete');
});

// Search routes
Route::get('/search', function () {
    return view('search.index');
})->name('search');

Route::post('/search/posts', function () {
    // Search posts logic
})->name('search.posts');

Route::post('/search/users', function () {
    // Search users logic
})->name('search.users');

// Notification routes
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', function () {
        return view('notifications.index');
    })->name('index');
    
    Route::patch('/{notification}/read', function () {
        // Mark notification as read
    })->name('read');
    
    Route::patch('/mark-all-read', function () {
        // Mark all notifications as read
    })->name('mark-all-read');
    
    Route::delete('/{notification}', function () {
        // Delete notification
    })->name('delete');
    
    Route::get('/unsubscribe', function () {
        // Unsubscribe from notifications
    })->name('unsubscribe');
});

// Settings routes with various HTTP methods
Route::middleware('auth')->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', function () {
        return view('settings.index');
    })->name('index');
    
    Route::get('/profile', function () {
        return view('settings.profile');
    })->name('profile');
    
    Route::patch('/profile', function () {
        // Update profile
    })->name('profile.update');
    
    Route::get('/security', function () {
        return view('settings.security');
    })->name('security');
    
    Route::put('/password', function () {
        // Change password
    })->name('password.update');
    
    Route::get('/notifications', function () {
        return view('settings.notifications');
    })->name('notifications');
    
    Route::patch('/notifications', function () {
        // Update notification preferences
    })->name('notifications.update');
    
    Route::get('/privacy', function () {
        return view('settings.privacy');
    })->name('privacy');
    
    Route::put('/privacy', function () {
        // Update privacy settings
    })->name('privacy.update');
    
    Route::delete('/account', function () {
        // Delete account
    })->name('account.delete');
});

// File upload routes
Route::middleware('auth')->group(function () {
    Route::post('/upload/image', function () {
        // Image upload logic
    })->name('upload.image');
    
    Route::post('/upload/file', function () {
        // File upload logic
    })->name('upload.file');
    
    Route::delete('/upload/{file}', function () {
        // Delete uploaded file
    })->name('upload.delete');
});

// Webhook routes (for external integrations)
Route::prefix('webhooks')->group(function () {
    Route::post('/github', function () {
        // GitHub webhook
    })->name('webhooks.github');
    
    Route::post('/stripe', function () {
        // Stripe webhook
    })->name('webhooks.stripe');
    
    Route::post('/mailgun', function () {
        // Mailgun webhook
    })->name('webhooks.mailgun');
});

// Health check and status routes
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
})->name('health');

Route::get('/status', function () {
    return response()->json([
        'status' => 'operational',
        'database' => 'connected',
        'cache' => 'active',
        'queue' => 'running'
    ]);
})->name('status');

// Sitemap and SEO routes
Route::get('/sitemap.xml', function () {
    return response()->view('sitemap')->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::get('/robots.txt', function () {
    return response('User-agent: *' . PHP_EOL . 'Allow: /', 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');
