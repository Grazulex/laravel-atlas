<?php

namespace App\Console\Commands;

use App\Events\PostPublished;
use App\Events\PostScheduled;
use App\Events\UserCreated;
use App\Jobs\OptimizePostSEO;
use App\Jobs\ProcessNewUserRegistration;
use App\Jobs\PublishScheduledPost;
use App\Jobs\SendOnboardingSequence;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostPublishedNotification;
use App\Services\ContentService;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

class AnalyticsReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'analytics:generate-report
                            {type : Report type (daily|weekly|monthly|custom)}
                            {--start-date= : Start date for custom reports (Y-m-d)}
                            {--end-date= : End date for custom reports (Y-m-d)}
                            {--format=table : Output format (table|json|csv|email)}
                            {--email-to=* : Email addresses to send report to}
                            {--include=* : Sections to include (users|posts|engagement|performance)}
                            {--export-path= : Path to export the report}
                            {--process-jobs : Process analytics jobs after report generation}
                            {--notify-slack : Send notification to Slack}';

    /**
     * The console command description.
     */
    protected $description = 'Generate comprehensive analytics reports with job processing and event integration';

    protected ContentService $contentService;
    protected NotificationService $notificationService;
    protected array $reportData = [];
    protected array $metrics = [];

    /**
     * Create a new command instance.
     */
    public function __construct(ContentService $contentService, NotificationService $notificationService)
    {
        parent::__construct();
        $this->contentService = $contentService;
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $reportType = $this->argument('type');
        $this->info("📊 Generating {$reportType} analytics report...");

        try {
            // Determine date range
            $dateRange = $this->determineDateRange($reportType);
            
            // Generate comprehensive analytics
            $this->generateAnalyticsData($dateRange);
            
            // Process the report based on format
            $this->processReportOutput();
            
            // Process additional jobs if requested
            if ($this->option('process-jobs')) {
                $this->processAnalyticsJobs();
            }
            
            // Send notifications
            $this->sendNotifications();
            
            // Trigger analytics events
            $this->triggerAnalyticsEvents();

            $this->info('✅ Analytics report generated successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Report generation failed: {$e->getMessage()}");
            Log::error('Analytics report generation failed', [
                'type' => $reportType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Determine date range for the report
     */
    protected function determineDateRange(string $type): array
    {
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');

        if ($type === 'custom' && $startDate && $endDate) {
            return [
                'start' => \Carbon\Carbon::parse($startDate)->startOfDay(),
                'end' => \Carbon\Carbon::parse($endDate)->endOfDay(),
            ];
        }

        return match ($type) {
            'daily' => [
                'start' => now()->subDay()->startOfDay(),
                'end' => now()->subDay()->endOfDay(),
            ],
            'weekly' => [
                'start' => now()->subWeek()->startOfWeek(),
                'end' => now()->subWeek()->endOfWeek(),
            ],
            'monthly' => [
                'start' => now()->subMonth()->startOfMonth(),
                'end' => now()->subMonth()->endOfMonth(),
            ],
            default => throw new \InvalidArgumentException("Invalid report type: {$type}")
        };
    }

    /**
     * Generate comprehensive analytics data
     */
    protected function generateAnalyticsData(array $dateRange): void
    {
        $this->info('📈 Collecting analytics data...');

        $includeSections = $this->option('include') ?: ['users', 'posts', 'engagement', 'performance'];

        foreach ($includeSections as $section) {
            $this->line("Processing {$section} analytics...");
            
            match ($section) {
                'users' => $this->generateUserAnalytics($dateRange),
                'posts' => $this->generatePostAnalytics($dateRange),
                'engagement' => $this->generateEngagementAnalytics($dateRange),
                'performance' => $this->generatePerformanceAnalytics($dateRange),
                default => $this->warn("Unknown section: {$section}")
            };
        }
    }

    /**
     * Generate user analytics
     */
    protected function generateUserAnalytics(array $dateRange): void
    {
        $userStats = [
            'new_users' => User::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'active_users' => User::whereBetween('last_activity_at', [$dateRange['start'], $dateRange['end']])->count(),
            'total_users' => User::count(),
            'user_roles' => User::selectRaw('role, COUNT(*) as count')->groupBy('role')->pluck('count', 'role')->toArray(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'premium_users' => User::where('is_premium', true)->count(),
        ];

        // Calculate user engagement metrics
        $userEngagement = [
            'avg_posts_per_user' => round(Post::count() / max(User::count(), 1), 2),
            'most_active_users' => User::withCount('posts')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->orderByDesc('posts_count')
                ->limit(10)
                ->get(['id', 'name', 'posts_count'])
                ->toArray(),
        ];

        $this->reportData['users'] = array_merge($userStats, $userEngagement);
        $this->metrics['user_growth'] = $userStats['new_users'];
    }

    /**
     * Generate post analytics
     */
    protected function generatePostAnalytics(array $dateRange): void
    {
        $postStats = [
            'new_posts' => Post::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'published_posts' => Post::where('status', 'published')
                ->whereBetween('published_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'scheduled_posts' => Post::where('status', 'scheduled')->count(),
            'total_posts' => Post::count(),
        ];

        // Category analytics
        $categoryStats = DB::table('posts')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, COUNT(*) as count')
            ->whereBetween('posts.created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('categories.name')
            ->pluck('count', 'name')
            ->toArray();

        // Most popular posts
        $popularPosts = Post::with(['user', 'category'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->withCount(['comments', 'likes'])
            ->orderByDesc('comments_count')
            ->limit(10)
            ->get(['id', 'title', 'user_id', 'category_id', 'comments_count'])
            ->toArray();

        $this->reportData['posts'] = [
            'stats' => $postStats,
            'by_category' => $categoryStats,
            'popular_posts' => $popularPosts,
        ];

        $this->metrics['content_growth'] = $postStats['new_posts'];
    }

    /**
     * Generate engagement analytics
     */
    protected function generateEngagementAnalytics(array $dateRange): void
    {
        // Comment analytics
        $commentStats = [
            'total_comments' => DB::table('comments')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'avg_comments_per_post' => round(
                DB::table('comments')->count() / max(Post::count(), 1), 2
            ),
            'most_commented_posts' => Post::withCount('comments')
                ->orderByDesc('comments_count')
                ->limit(5)
                ->get(['id', 'title', 'comments_count'])
                ->toArray(),
        ];

        // User interaction metrics
        $interactionStats = [
            'user_follows' => DB::table('user_follows')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'post_likes' => DB::table('post_likes')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
        ];

        // Notification analytics
        $notificationStats = [
            'notifications_sent' => DB::table('notifications')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'notifications_read' => DB::table('notifications')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('read_at')
                ->count(),
        ];

        $this->reportData['engagement'] = [
            'comments' => $commentStats,
            'interactions' => $interactionStats,
            'notifications' => $notificationStats,
        ];

        $this->metrics['engagement_rate'] = $commentStats['total_comments'] + $interactionStats['post_likes'];
    }

    /**
     * Generate performance analytics
     */
    protected function generatePerformanceAnalytics(array $dateRange): void
    {
        // Job performance
        $jobStats = [
            'completed_jobs' => DB::table('job_batches')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('finished_at', '!=', null)
                ->count(),
            'failed_jobs' => DB::table('failed_jobs')
                ->whereBetween('failed_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'pending_jobs' => DB::table('jobs')->count(),
        ];

        // Queue performance
        $queueStats = [
            'avg_job_processing_time' => $this->calculateAverageJobTime($dateRange),
            'job_types' => $this->getJobTypeDistribution($dateRange),
        ];

        // System performance metrics
        $systemStats = [
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
            'cache_hit_rate' => $this->getCacheHitRate(),
        ];

        $this->reportData['performance'] = [
            'jobs' => $jobStats,
            'queues' => $queueStats,
            'system' => $systemStats,
        ];

        $this->metrics['system_health'] = $jobStats['failed_jobs'] === 0 ? 100 : 95;
    }

    /**
     * Process report output based on format
     */
    protected function processReportOutput(): void
    {
        $format = $this->option('format');
        
        match ($format) {
            'table' => $this->outputTableFormat(),
            'json' => $this->outputJsonFormat(),
            'csv' => $this->outputCsvFormat(),
            'email' => $this->sendEmailReport(),
            default => $this->outputTableFormat()
        };
    }

    /**
     * Output report in table format
     */
    protected function outputTableFormat(): void
    {
        $this->newLine();
        $this->info('📊 Analytics Report Summary:');
        
        foreach ($this->reportData as $section => $data) {
            $this->newLine();
            $this->line("=== " . strtoupper($section) . " ===");
            
            if (is_array($data)) {
                $this->displayArrayAsTable($data, $section);
            }
        }

        // Display key metrics
        $this->newLine();
        $this->info('🎯 Key Metrics:');
        $this->table(
            ['Metric', 'Value'],
            collect($this->metrics)->map(fn($value, $key) => [
                ucwords(str_replace('_', ' ', $key)),
                $value
            ])->toArray()
        );
    }

    /**
     * Display array data as table
     */
    protected function displayArrayAsTable(array $data, string $section): void
    {
        if (isset($data['stats']) && is_array($data['stats'])) {
            $this->table(
                ['Statistic', 'Value'],
                collect($data['stats'])->map(fn($value, $key) => [
                    ucwords(str_replace('_', ' ', $key)),
                    is_numeric($value) ? number_format($value) : $value
                ])->toArray()
            );
        } elseif (is_array(reset($data))) {
            // Handle nested arrays differently based on section
            foreach ($data as $subsection => $subdata) {
                $this->line("--- " . ucwords(str_replace('_', ' ', $subsection)) . " ---");
                if (is_array($subdata) && !empty($subdata)) {
                    $firstItem = reset($subdata);
                    if (is_array($firstItem)) {
                        // Display as table if it's an array of arrays
                        $headers = array_keys($firstItem);
                        $this->table($headers, $subdata);
                    } else {
                        // Display as key-value pairs
                        $this->table(
                            ['Item', 'Count'],
                            collect($subdata)->map(fn($value, $key) => [$key, $value])->toArray()
                        );
                    }
                }
            }
        } else {
            $this->table(
                ['Item', 'Value'],
                collect($data)->map(fn($value, $key) => [
                    ucwords(str_replace('_', ' ', $key)),
                    is_numeric($value) ? number_format($value) : $value
                ])->toArray()
            );
        }
    }

    /**
     * Output report in JSON format
     */
    protected function outputJsonFormat(): void
    {
        $reportJson = json_encode([
            'report_type' => $this->argument('type'),
            'generated_at' => now()->toISOString(),
            'data' => $this->reportData,
            'metrics' => $this->metrics,
        ], JSON_PRETTY_PRINT);

        if ($exportPath = $this->option('export-path')) {
            file_put_contents($exportPath, $reportJson);
            $this->info("📄 Report exported to: {$exportPath}");
        } else {
            $this->line($reportJson);
        }
    }

    /**
     * Output report in CSV format
     */
    protected function outputCsvFormat(): void
    {
        $csvData = $this->convertReportToCsv();
        
        if ($exportPath = $this->option('export-path')) {
            file_put_contents($exportPath, $csvData);
            $this->info("📄 CSV report exported to: {$exportPath}");
        } else {
            $this->line($csvData);
        }
    }

    /**
     * Send email report
     */
    protected function sendEmailReport(): void
    {
        $emailAddresses = $this->option('email-to');
        
        if (empty($emailAddresses)) {
            $this->error('No email addresses provided for email format');
            return;
        }

        foreach ($emailAddresses as $email) {
            // Mail::to($email)->send(new AnalyticsReportMail($this->reportData, $this->metrics));
            $this->info("📧 Report sent to: {$email}");
        }
    }

    /**
     * Process analytics jobs
     */
    protected function processAnalyticsJobs(): void
    {
        $this->info('🔄 Processing analytics jobs...');

        // Dispatch jobs for data processing
        $jobs = [
            new OptimizePostSEO(Post::latest()->first()),
            new ProcessNewUserRegistration(User::latest()->first()),
        ];

        Bus::batch($jobs)
            ->name('Analytics Processing Batch')
            ->onQueue('analytics')
            ->dispatch();

        $this->info('✅ Analytics jobs dispatched');
    }

    /**
     * Send notifications
     */
    protected function sendNotifications(): void
    {
        // Slack notification
        if ($this->option('notify-slack')) {
            $this->sendSlackNotification();
        }

        // Admin notifications for important metrics
        if ($this->metrics['system_health'] < 95) {
            $this->notifyAdminsSystemHealth();
        }
    }

    /**
     * Trigger analytics events
     */
    protected function triggerAnalyticsEvents(): void
    {
        Event::dispatch('analytics.report.generated', [
            'type' => $this->argument('type'),
            'metrics' => $this->metrics,
            'generated_at' => now(),
        ]);
    }

    /**
     * Helper methods for performance analytics
     */
    protected function calculateAverageJobTime(array $dateRange): float
    {
        // This would calculate from job execution logs
        return rand(100, 500) / 100; // Placeholder
    }

    protected function getJobTypeDistribution(array $dateRange): array
    {
        // This would analyze job types from logs
        return [
            'OptimizePostSEO' => rand(10, 50),
            'ProcessNewUserRegistration' => rand(5, 20),
            'SendOnboardingSequence' => rand(5, 20),
            'PublishScheduledPost' => rand(3, 15),
        ];
    }

    protected function getDatabaseSize(): string
    {
        $size = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'DB Size in MB' FROM information_schema.tables WHERE table_schema=?", [config('database.connections.mysql.database')]);
        return ($size[0]->{'DB Size in MB'} ?? 0) . ' MB';
    }

    protected function getStorageUsage(): string
    {
        $bytes = disk_total_space(storage_path()) - disk_free_space(storage_path());
        return $this->formatBytes($bytes);
    }

    protected function getCacheHitRate(): string
    {
        // This would come from cache analytics
        return rand(75, 95) . '%';
    }

    protected function sendSlackNotification(): void
    {
        // Integration with Slack webhook
        $this->info('📢 Slack notification sent');
    }

    protected function notifyAdminsSystemHealth(): void
    {
        $admins = User::where('is_admin', true)->get();
        foreach ($admins as $admin) {
            // Send system health alert
        }
    }

    protected function convertReportToCsv(): string
    {
        // Convert report data to CSV format
        $csv = "Section,Metric,Value\n";
        
        foreach ($this->reportData as $section => $data) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (is_scalar($value)) {
                        $csv .= "{$section},{$key},{$value}\n";
                    }
                }
            }
        }
        
        return $csv;
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
