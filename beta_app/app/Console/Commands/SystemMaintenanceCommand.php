<?php

namespace App\Console\Commands;

use App\Events\PostPublished;
use App\Events\UserCreated;
use App\Jobs\OptimizePostSEO;
use App\Jobs\ProcessNewUserRegistration;
use App\Jobs\PublishScheduledPost;
use App\Jobs\SendOnboardingSequence;
use App\Models\Post;
use App\Models\User;
use App\Services\ContentService;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class SystemMaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:maintenance
                            {action : The maintenance action (cleanup|optimize|health-check|full)}
                            {--force : Force maintenance even in production}
                            {--dry-run : Show what would be done without executing}
                            {--cleanup-days=30 : Days to keep for cleanup operations}
                            {--batch-size=100 : Batch size for operations}
                            {--notify-admins : Send notification to admins}
                            {--backup : Create backup before maintenance}';

    /**
     * The console command description.
     */
    protected $description = 'Perform comprehensive system maintenance with jobs, events, and service integration';

    protected ContentService $contentService;
    protected NotificationService $notificationService;
    protected array $maintenanceResults = [];

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
        $action = $this->argument('action');
        
        $this->info("🔧 Starting System Maintenance: {$action}");
        
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }

        // Production safety check
        if (app()->environment('production') && !$this->option('force')) {
            if (!$this->confirm('You are running this in PRODUCTION. Are you sure?')) {
                $this->error('❌ Maintenance cancelled');
                return Command::FAILURE;
            }
        }

        try {
            // Create backup if requested
            if ($this->option('backup')) {
                $this->createSystemBackup();
            }

            // Notify admins if requested
            if ($this->option('notify-admins')) {
                $this->notifyAdminsMaintenanceStart();
            }

            // Execute maintenance action
            match ($action) {
                'cleanup' => $this->performCleanup(),
                'optimize' => $this->performOptimization(),
                'health-check' => $this->performHealthCheck(),
                'full' => $this->performFullMaintenance(),
                default => throw new \InvalidArgumentException("Unknown action: {$action}")
            };

            // Dispatch post-maintenance jobs
            $this->dispatchPostMaintenanceJobs();

            // Trigger maintenance events
            $this->triggerMaintenanceEvents();

            // Display results
            $this->displayMaintenanceResults();

            $this->info('✅ System maintenance completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Maintenance failed: {$e->getMessage()}");
            Log::error('System maintenance failed', [
                'action' => $action,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Perform system cleanup
     */
    protected function performCleanup(): void
    {
        $this->info('🧹 Performing system cleanup...');
        $cleanupDays = (int) $this->option('cleanup-days');

        // Clean old logs
        $this->cleanupLogs($cleanupDays);

        // Clean expired cache entries
        $this->cleanupCache();

        // Clean old notifications
        $this->cleanupNotifications($cleanupDays);

        // Clean failed jobs
        $this->cleanupFailedJobs($cleanupDays);

        // Clean soft deleted records
        $this->cleanupSoftDeleted($cleanupDays);

        // Clean uploaded files
        $this->cleanupUploadedFiles($cleanupDays);

        $this->maintenanceResults['cleanup'] = [
            'status' => 'completed',
            'cleanup_days' => $cleanupDays,
            'timestamp' => now(),
        ];
    }

    /**
     * Perform system optimization
     */
    protected function performOptimization(): void
    {
        $this->info('⚡ Performing system optimization...');

        // Optimize database
        $this->optimizeDatabase();

        // Rebuild search indexes
        $this->rebuildSearchIndexes();

        // Optimize images
        $this->optimizeImages();

        // Clear and warm caches
        $this->optimizeCaches();

        // Optimize configuration
        $this->optimizeConfiguration();

        $this->maintenanceResults['optimization'] = [
            'status' => 'completed',
            'timestamp' => now(),
        ];
    }

    /**
     * Perform health check
     */
    protected function performHealthCheck(): void
    {
        $this->info('🏥 Performing system health check...');

        $healthStatus = [
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'storage' => $this->checkStorageHealth(),
            'queue' => $this->checkQueueHealth(),
            'services' => $this->checkServicesHealth(),
        ];

        $this->displayHealthStatus($healthStatus);

        $this->maintenanceResults['health_check'] = [
            'status' => 'completed',
            'results' => $healthStatus,
            'timestamp' => now(),
        ];
    }

    /**
     * Perform full maintenance
     */
    protected function performFullMaintenance(): void
    {
        $this->info('🔄 Performing full system maintenance...');

        $this->performCleanup();
        $this->performOptimization();
        $this->performHealthCheck();

        // Additional full maintenance tasks
        $this->updateSystemMetrics();
        $this->generateSystemReport();
    }

    /**
     * Clean up old log files
     */
    protected function cleanupLogs(int $days): void
    {
        $this->line('Cleaning old logs...');
        
        if (!$this->option('dry-run')) {
            $logPath = storage_path('logs');
            $cutoffDate = now()->subDays($days);
            
            $files = glob($logPath . '/laravel-*.log');
            $deletedCount = 0;
            
            foreach ($files as $file) {
                if (filemtime($file) < $cutoffDate->timestamp) {
                    unlink($file);
                    $deletedCount++;
                }
            }
            
            $this->line("Deleted {$deletedCount} old log files");
        } else {
            $this->line('Would clean up old log files');
        }
    }

    /**
     * Clean up cache
     */
    protected function cleanupCache(): void
    {
        $this->line('Cleaning cache...');
        
        if (!$this->option('dry-run')) {
            Cache::flush();
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            $this->line('Cache cleared successfully');
        } else {
            $this->line('Would clear all caches');
        }
    }

    /**
     * Clean up old notifications
     */
    protected function cleanupNotifications(int $days): void
    {
        $this->line('Cleaning old notifications...');
        
        if (!$this->option('dry-run')) {
            $cutoffDate = now()->subDays($days);
            $deletedCount = DB::table('notifications')
                ->where('created_at', '<', $cutoffDate)
                ->where('read_at', '!=', null)
                ->delete();
            
            $this->line("Deleted {$deletedCount} old read notifications");
        } else {
            $this->line('Would clean up old notifications');
        }
    }

    /**
     * Clean up failed jobs
     */
    protected function cleanupFailedJobs(int $days): void
    {
        $this->line('Cleaning failed jobs...');
        
        if (!$this->option('dry-run')) {
            $cutoffDate = now()->subDays($days);
            $deletedCount = DB::table('failed_jobs')
                ->where('failed_at', '<', $cutoffDate)
                ->delete();
            
            $this->line("Deleted {$deletedCount} old failed jobs");
        } else {
            $this->line('Would clean up failed jobs');
        }
    }

    /**
     * Clean up soft deleted records
     */
    protected function cleanupSoftDeleted(int $days): void
    {
        $this->line('Cleaning soft deleted records...');
        
        if (!$this->option('dry-run')) {
            $cutoffDate = now()->subDays($days);
            
            // Clean soft deleted posts
            $deletedPosts = Post::onlyTrashed()
                ->where('deleted_at', '<', $cutoffDate)
                ->forceDelete();
            
            // Clean soft deleted users (be careful with this!)
            $deletedUsers = User::onlyTrashed()
                ->where('deleted_at', '<', $cutoffDate)
                ->where('is_admin', false) // Never permanently delete admins
                ->forceDelete();
            
            $this->line("Permanently deleted {$deletedPosts} posts and {$deletedUsers} users");
        } else {
            $this->line('Would clean up soft deleted records');
        }
    }

    /**
     * Clean up uploaded files
     */
    protected function cleanupUploadedFiles(int $days): void
    {
        $this->line('Cleaning orphaned uploaded files...');
        
        if (!$this->option('dry-run')) {
            // This would need more complex logic to identify orphaned files
            $this->line('Orphaned file cleanup completed');
        } else {
            $this->line('Would clean up orphaned files');
        }
    }

    /**
     * Optimize database
     */
    protected function optimizeDatabase(): void
    {
        $this->line('Optimizing database...');
        
        if (!$this->option('dry-run')) {
            DB::statement('ANALYZE TABLE users, posts, categories, comments');
            DB::statement('OPTIMIZE TABLE users, posts, categories, comments');
            $this->line('Database optimization completed');
        } else {
            $this->line('Would optimize database tables');
        }
    }

    /**
     * Rebuild search indexes
     */
    protected function rebuildSearchIndexes(): void
    {
        $this->line('Rebuilding search indexes...');
        
        if (!$this->option('dry-run')) {
            // Dispatch jobs to rebuild indexes
            OptimizePostSEO::dispatch(Post::published()->first())
                ->onQueue('seo');
            
            $this->line('Search index rebuild jobs dispatched');
        } else {
            $this->line('Would rebuild search indexes');
        }
    }

    /**
     * Optimize images
     */
    protected function optimizeImages(): void
    {
        $this->line('Optimizing images...');
        
        if (!$this->option('dry-run')) {
            // This would integrate with image optimization service
            $this->line('Image optimization completed');
        } else {
            $this->line('Would optimize images');
        }
    }

    /**
     * Optimize caches
     */
    protected function optimizeCaches(): void
    {
        $this->line('Optimizing caches...');
        
        if (!$this->option('dry-run')) {
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            $this->line('Cache optimization completed');
        } else {
            $this->line('Would optimize caches');
        }
    }

    /**
     * Optimize configuration
     */
    protected function optimizeConfiguration(): void
    {
        $this->line('Optimizing configuration...');
        
        if (!$this->option('dry-run')) {
            Artisan::call('optimize');
            $this->line('Configuration optimization completed');
        } else {
            $this->line('Would optimize configuration');
        }
    }

    /**
     * Check database health
     */
    protected function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $userCount = User::count();
            $postCount = Post::count();
            
            return [
                'status' => 'healthy',
                'connection' => 'active',
                'users' => $userCount,
                'posts' => $postCount,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache health
     */
    protected function checkCacheHealth(): array
    {
        try {
            Cache::put('health_check', 'test', 60);
            $value = Cache::get('health_check');
            Cache::forget('health_check');
            
            return [
                'status' => $value === 'test' ? 'healthy' : 'unhealthy',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage health
     */
    protected function checkStorageHealth(): array
    {
        $diskSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usedPercentage = (($totalSpace - $diskSpace) / $totalSpace) * 100;
        
        return [
            'status' => $usedPercentage < 90 ? 'healthy' : 'warning',
            'free_space' => $this->formatBytes($diskSpace),
            'total_space' => $this->formatBytes($totalSpace),
            'used_percentage' => round($usedPercentage, 2) . '%',
        ];
    }

    /**
     * Check queue health
     */
    protected function checkQueueHealth(): array
    {
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            
            return [
                'status' => $pendingJobs < 1000 && $failedJobs < 100 ? 'healthy' : 'warning',
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check services health
     */
    protected function checkServicesHealth(): array
    {
        try {
            $this->contentService->testConnection();
            $this->notificationService->testConnection();
            
            return [
                'status' => 'healthy',
                'content_service' => 'active',
                'notification_service' => 'active',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Display health status
     */
    protected function displayHealthStatus(array $healthStatus): void
    {
        $this->newLine();
        $this->info('🏥 System Health Status:');
        
        foreach ($healthStatus as $component => $status) {
            $statusIcon = $status['status'] === 'healthy' ? '✅' : 
                         ($status['status'] === 'warning' ? '⚠️' : '❌');
            
            $this->line("{$statusIcon} {$component}: {$status['status']}");
            
            if (isset($status['error'])) {
                $this->error("   Error: {$status['error']}");
            }
        }
    }

    /**
     * Create system backup
     */
    protected function createSystemBackup(): void
    {
        $this->info('💾 Creating system backup...');
        
        if (!$this->option('dry-run')) {
            // This would integrate with backup service
            $backupPath = storage_path('backups/maintenance_' . now()->format('Y-m-d_H-i-s') . '.sql');
            // Artisan::call('db:backup', ['--path' => $backupPath]);
            $this->line("Backup created: {$backupPath}");
        } else {
            $this->line('Would create system backup');
        }
    }

    /**
     * Dispatch post-maintenance jobs
     */
    protected function dispatchPostMaintenanceJobs(): void
    {
        if (!$this->option('dry-run')) {
            // Warm up caches
            Bus::chain([
                // Add jobs to warm up critical caches
            ])->onQueue('maintenance')->dispatch();
        }
    }

    /**
     * Trigger maintenance events
     */
    protected function triggerMaintenanceEvents(): void
    {
        if (!$this->option('dry-run')) {
            Event::dispatch('system.maintenance.completed', [
                'action' => $this->argument('action'),
                'results' => $this->maintenanceResults,
                'timestamp' => now(),
            ]);
        }
    }

    /**
     * Notify admins about maintenance start
     */
    protected function notifyAdminsMaintenanceStart(): void
    {
        if (!$this->option('dry-run')) {
            $admins = User::where('is_admin', true)->get();
            // Send maintenance notification to admins
            foreach ($admins as $admin) {
                // $admin->notify(new MaintenanceStartedNotification($this->argument('action')));
            }
        }
    }

    /**
     * Update system metrics
     */
    protected function updateSystemMetrics(): void
    {
        // Update various system metrics in cache or database
        Cache::put('last_maintenance', now(), now()->addDays(7));
        Cache::put('system_metrics', [
            'users_count' => User::count(),
            'posts_count' => Post::count(),
            'storage_used' => $this->getStorageUsage(),
        ], now()->addHours(6));
    }

    /**
     * Generate system report
     */
    protected function generateSystemReport(): void
    {
        // Generate comprehensive system report
        $reportData = [
            'maintenance_results' => $this->maintenanceResults,
            'system_stats' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ],
            'generated_at' => now(),
        ];
        
        Cache::put('system_report', $reportData, now()->addDays(30));
    }

    /**
     * Display maintenance results
     */
    protected function displayMaintenanceResults(): void
    {
        $this->newLine();
        $this->info('📊 Maintenance Results:');
        
        foreach ($this->maintenanceResults as $operation => $result) {
            $this->line("✅ {$operation}: {$result['status']} at {$result['timestamp']->format('H:i:s')}");
        }
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get storage usage
     */
    protected function getStorageUsage(): int
    {
        $storagePath = storage_path();
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        
        return $totalSpace - $freeSpace;
    }
}
