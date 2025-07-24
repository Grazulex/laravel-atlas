<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\User')->only(['index']);
        $this->middleware('throttle:30,1')->only(['store', 'update']);
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $query = User::with(['profile', 'posts'])
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%'));

        $users = $query->paginate(20);

        if ($request->expectsJson()) {
            return UserResource::collection($users);
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
            'start_date' => $request->start_date,
            'notification_preferences' => $request->notification_preferences ?? [],
        ]);

        // Create profile if provided
        if ($request->has('profile')) {
            $user->profile()->create($request->profile);
        }

        if ($request->expectsJson()) {
            return new UserResource($user->load('profile'));
        }

        return redirect()->route('users.show', $user)
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user
     */
    public function show(User $user, Request $request)
    {
        Gate::authorize('view', $user);

        $user->load(['profile', 'posts' => fn($q) => $q->published()->latest()->take(5)]);

        if ($request->expectsJson()) {
            return new UserResource($user);
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|string',
            'department' => 'nullable|string',
            'notification_preferences' => 'nullable|array',
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return new UserResource($user->load('profile'));
        }

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user, Request $request)
    {
        Gate::authorize('delete', $user);

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You cannot delete yourself'], 403);
            }
            return back()->with('error', 'You cannot delete yourself!');
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'User deleted successfully']);
        }

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Show user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user()->load(['profile', 'posts', 'notifications']);
        
        $stats = [
            'posts_count' => $user->posts()->count(),
            'published_posts_count' => $user->posts()->published()->count(),
            'draft_posts_count' => $user->posts()->draft()->count(),
            'comments_count' => $user->comments()->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        return view('users.dashboard', compact('user', 'stats'));
    }

    /**
     * Show user profile
     */
    public function profile(User $user = null)
    {
        $user = $user ?? Auth::user();
        $user->load(['profile', 'posts' => fn($q) => $q->published()->latest()->paginate(10)]);

        return view('users.profile', compact('user'));
    }

    /**
     * Follow/unfollow a user
     */
    public function toggleFollow(User $user, Request $request)
    {
        $currentUser = Auth::user();
        
        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 403);
        }

        $isFollowing = $currentUser->following()->where('followed_user_id', $user->id)->exists();

        if ($isFollowing) {
            $currentUser->following()->detach($user->id);
            $message = 'User unfollowed';
        } else {
            $currentUser->following()->attach($user->id);
            $message = 'User followed';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'is_following' => !$isFollowing,
                'followers_count' => $user->followers()->count()
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get user followers
     */
    public function followers(User $user, Request $request)
    {
        Gate::authorize('view', $user);

        $followers = $user->followers()->paginate(20);

        if ($request->expectsJson()) {
            return UserResource::collection($followers);
        }

        return view('users.followers', compact('user', 'followers'));
    }

    /**
     * Get users that this user is following
     */
    public function following(User $user, Request $request)
    {
        Gate::authorize('view', $user);

        $following = $user->following()->paginate(20);

        if ($request->expectsJson()) {
            return UserResource::collection($following);
        }

        return view('users.following', compact('user', 'following'));
    }

    /**
     * Bulk actions on users
     */
    public function bulkAction(Request $request)
    {
        Gate::authorize('bulkAction', User::class);

        $validated = $request->validate([
            'action' => 'required|in:delete,activate,deactivate,change_role',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'new_role' => 'required_if:action,change_role|string',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $processed = 0;

        foreach ($users as $user) {
            // Skip current user for safety
            if ($user->id === Auth::id()) {
                continue;
            }

            switch ($validated['action']) {
                case 'delete':
                    if (Auth::user()->can('delete', $user)) {
                        $user->delete();
                        $processed++;
                    }
                    break;
                    
                case 'activate':
                    $user->update(['is_active' => true]);
                    $processed++;
                    break;
                    
                case 'deactivate':
                    $user->update(['is_active' => false]);
                    $processed++;
                    break;
                    
                case 'change_role':
                    if (Auth::user()->can('changeRole', $user)) {
                        $user->update(['role' => $validated['new_role']]);
                        $processed++;
                    }
                    break;
            }
        }

        return response()->json([
            'message' => "Bulk action completed. {$processed} users processed.",
            'processed' => $processed
        ]);
    }
}
