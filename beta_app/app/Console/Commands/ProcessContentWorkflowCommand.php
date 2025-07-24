<?php

namespace App\Console\Commands;

use App\Actions\CreateUserAction;
use App\Actions\PublishPostAction;
use App\Events\PostPublished;
use App\Events\UserCreated;
use App\Jobs\OptimizePostSEO;
use App\Jobs\ProcessNewUserRegistration;
use App\Jobs\PublishScheduledPost;
use App\Jobs\SendOnboardingSequence;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostPublishedNotification;
use App\Notifications\WelcomeNewUserNotification;
use App\Services\ContentService;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProcessContentWorkflowCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'workflow:process-content
                            {--users=5 : Number of users to create}
                            {--posts=20 : Number of posts to create}
                            {--publish-delay=30 : Delay in seconds before publishing}
                            {--batch-size=10 : Batch size for processing}
                            {--dry-run : Run without making changes}
                            {--force : Force execution even if jobs are queued}';

    /**
     * The console command description.
     */
    protected $description = 'Process a complete content workflow with users, posts, jobs, events, and notifications';

    protected ContentService $contentService;
    protected NotificationService $notificationService;
    protected CreateUserAction $createUserAction;
    protected PublishPostAction $publishPostAction;

    /**
     * Create a new command instance.
     */
    public function __construct(
        ContentService $contentService,
        NotificationService $notificationService,
        CreateUserAction $createUserAction,
        PublishPostAction $publishPostAction
    ) {
        parent::__construct();
        $this->contentService = $contentService;
        $this->notificationService = $notificationService;
        $this->createUserAction = $createUserAction;
        $this->publishPostAction = $publishPostAction;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting Content Workflow Process...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }

        try {
            DB::beginTransaction();

            // Step 1: Check prerequisites
            $this->checkPrerequisites();

            // Step 2: Create users with full workflow
            $users = $this->createUsersWorkflow();

            // Step 3: Create posts with complex workflow
            $posts = $this->createPostsWorkflow($users);

            // Step 4: Process scheduled content
            $this->processScheduledContent();

            // Step 5: Dispatch batch jobs
            $this->dispatchBatchJobs($users, $posts);

            // Step 6: Trigger events and notifications
            $this->triggerEventsAndNotifications($users, $posts);

            if (!$this->option('dry-run')) {
                DB::commit();
                $this->info('✅ Content workflow completed successfully!');
            } else {
                DB::rollBack();
                $this->info('✅ Dry run completed - no changes were made');
            }

            // Step 7: Display summary
            $this->displaySummary($users, $posts);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Workflow failed: ' . $e->getMessage());
            Log::error('Content workflow failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'options' => $this->options(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Check prerequisites before running workflow
     */
    protected function checkPrerequisites(): void
    {
        $this->info('🔍 Checking prerequisites...');

        // Check if categories exist
        if (Category::count() === 0) {
            $this->warn('No categories found, creating default categories...');
            $this->createDefaultCategories();
        }

        // Check queue connection
        try {
            $this->contentService->testConnection();
            $this->info('✅ Content service connection OK');
        } catch (\Exception $e) {
            throw new \Exception('Content service unavailable: ' . $e->getMessage());
        }

        // Check if forced or no pending jobs
        if (!$this->option('force')) {
            $pendingJobs = DB::table('jobs')->count();
            if ($pendingJobs > 100) {
                throw new \Exception("Too many pending jobs ({$pendingJobs}). Use --force to override.");
            }
        }

        $this->info('✅ Prerequisites check passed');
    }

    /**
     * Create users with full workflow
     */
    protected function createUsersWorkflow(): array
    {
        $userCount = (int) $this->option('users');
        $this->info("👥 Creating {$userCount} users with full workflow...");

        $users = [];
        $progressBar = $this->output->createProgressBar($userCount);

        for ($i = 0; $i < $userCount; $i++) {
            $userData = [
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => 'password123',
                'role' => fake()->randomElement(['author', 'editor', 'contributor']),
                'profile' => [
                    'bio' => fake()->paragraph(),
                    'website' => fake()->url(),
                    'avatar' => fake()->imageUrl(200, 200, 'people'),
                ],
                'notification_preferences' => [
                    'email_notifications' => fake()->boolean(80),
                    'sms_notifications' => fake()->boolean(30),
                    'push_notifications' => fake()->boolean(90),
                ],
            ];

            if (!$this->option('dry-run')) {
                // Use our action to create user (triggers events)
                $user = $this->createUserAction->execute($userData);
                $users[] = $user;

                // Dispatch onboarding job
                ProcessNewUserRegistration::dispatch($user)
                    ->onQueue('users')
                    ->delay(now()->addSeconds(rand(10, 60)));

                // Send welcome notification
                $user->notify(new WelcomeNewUserNotification($user, $this->notificationService));
            } else {
                $users[] = (object) $userData;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✅ Created {$userCount} users");

        return $users;
    }

    /**
     * Create posts with complex workflow
     */
    protected function createPostsWorkflow(array $users): array
    {
        $postCount = (int) $this->option('posts');
        $this->info("📝 Creating {$postCount} posts with complex workflow...");

        $posts = [];
        $categories = Category::all();
        $progressBar = $this->output->createProgressBar($postCount);

        for ($i = 0; $i < $postCount; $i++) {
            $user = fake()->randomElement($users);
            $category = fake()->randomElement($categories);
            
            $postData = [
                'title' => fake()->sentence(rand(4, 8)),
                'content' => fake()->paragraphs(rand(5, 15), true),
                'excerpt' => fake()->paragraph(),
                'category_id' => $category->id,
                'status' => fake()->randomElement(['draft', 'published', 'scheduled']),
                'featured_image' => fake()->imageUrl(800, 400, 'technology'),
                'meta_title' => fake()->sentence(6),
                'meta_description' => fake()->paragraph(2),
            ];

            // Add published_at for scheduled posts
            if ($postData['status'] === 'scheduled') {
                $postData['published_at'] = now()->addHours(rand(1, 72));
            }

            if (!$this->option('dry-run')) {
                // Use our action to publish post (triggers events)
                if ($postData['status'] === 'published') {
                    $post = $this->publishPostAction->execute(
                        is_object($user) ? $user : User::find($user->id),
                        $postData
                    );
                } else {
                    $post = Post::create(array_merge($postData, [
                        'user_id' => is_object($user) ? $user->id : $user->id,
                        'slug' => \Str::slug($postData['title']),
                    ]));
                }

                $posts[] = $post;

                // Schedule SEO optimization
                if ($post->status === 'published') {
                    OptimizePostSEO::dispatch($post)
                        ->onQueue('seo')
                        ->delay(now()->addMinutes(rand(5, 30)));
                }

                // Schedule publishing for drafts
                if ($post->status === 'scheduled') {
                    PublishScheduledPost::dispatch($post)
                        ->onQueue('content')
                        ->delay($post->published_at);
                }
            } else {
                $posts[] = (object) $postData;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✅ Created {$postCount} posts");

        return $posts;
    }

    /**
     * Process scheduled content
     */
    protected function processScheduledContent(): void
    {
        $this->info('⏰ Processing scheduled content...');

        if (!$this->option('dry-run')) {
            $scheduledPosts = Post::where('status', 'scheduled')
                ->where('published_at', '<=', now())
                ->get();

            foreach ($scheduledPosts as $post) {
                PublishScheduledPost::dispatch($post)->onQueue('content');
            }

            $this->info("✅ Queued {$scheduledPosts->count()} scheduled posts for publishing");
        } else {
            $this->info('✅ Would process scheduled posts (dry run)');
        }
    }

    /**
     * Dispatch batch jobs
     */
    protected function dispatchBatchJobs(array $users, array $posts): void
    {
        $this->info('🔄 Dispatching batch jobs...');

        if (!$this->option('dry-run')) {
            $batchSize = (int) $this->option('batch-size');
            
            // Batch user onboarding
            $userBatches = array_chunk($users, $batchSize);
            foreach ($userBatches as $batchIndex => $userBatch) {
                $jobs = [];
                foreach ($userBatch as $user) {
                    $jobs[] = new SendOnboardingSequence($user);
                }

                Bus::batch($jobs)
                    ->name("User Onboarding Batch #{$batchIndex}")
                    ->onQueue('users')
                    ->dispatch();
            }

            // Batch SEO optimization
            $publishedPosts = array_filter($posts, function($post) {
                return is_object($post) && isset($post->status) && $post->status === 'published';
            });

            $postBatches = array_chunk($publishedPosts, $batchSize);
            foreach ($postBatches as $batchIndex => $postBatch) {
                $jobs = [];
                foreach ($postBatch as $post) {
                    $jobs[] = new OptimizePostSEO($post);
                }

                Bus::batch($jobs)
                    ->name("SEO Optimization Batch #{$batchIndex}")
                    ->onQueue('seo')
                    ->dispatch();
            }

            $this->info('✅ Batch jobs dispatched');
        } else {
            $this->info('✅ Would dispatch batch jobs (dry run)');
        }
    }

    /**
     * Trigger events and notifications
     */
    protected function triggerEventsAndNotifications(array $users, array $posts): void
    {
        $this->info('📢 Triggering events and notifications...');

        if (!$this->option('dry-run')) {
            // Trigger user events
            foreach (array_slice($users, 0, 3) as $user) {
                Event::dispatch(new UserCreated($user));
            }

            // Trigger post events
            $publishedPosts = array_filter($posts, function($post) {
                return is_object($post) && isset($post->status) && $post->status === 'published';
            });

            foreach (array_slice($publishedPosts, 0, 5) as $post) {
                Event::dispatch(new PostPublished($post));
                
                // Send notifications to followers
                $followers = User::whereHas('following', function($query) use ($post) {
                    $query->where('followed_user_id', $post->user_id);
                })->get();

                foreach ($followers as $follower) {
                    $follower->notify(new PostPublishedNotification($post, $this->contentService));
                }
            }

            $this->info('✅ Events and notifications triggered');
        } else {
            $this->info('✅ Would trigger events and notifications (dry run)');
        }
    }

    /**
     * Create default categories
     */
    protected function createDefaultCategories(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Tech related posts'],
            ['name' => 'Business', 'slug' => 'business', 'description' => 'Business and entrepreneurship'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Lifestyle and wellness'],
            ['name' => 'Education', 'slug' => 'education', 'description' => 'Educational content'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }

    /**
     * Display summary of the workflow
     */
    protected function displaySummary(array $users, array $posts): void
    {
        $this->newLine();
        $this->info('📊 Workflow Summary:');
        $this->table(
            ['Component', 'Count', 'Status'],
            [
                ['Users Created', count($users), '✅'],
                ['Posts Created', count($posts), '✅'],
                ['Jobs Dispatched', 'Multiple batches', '✅'],
                ['Events Triggered', 'UserCreated, PostPublished', '✅'],
                ['Notifications Sent', 'Welcome, PostPublished', '✅'],
            ]
        );

        if (!$this->option('dry-run')) {
            $this->info('🎯 Next Steps:');
            $this->line('- Check queue workers: php artisan queue:work');
            $this->line('- Monitor jobs: php artisan queue:monitor');
            $this->line('- View logs: tail -f storage/logs/laravel.log');
        }
    }
}
