<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DataExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:export 
                            {table? : The table to export (optional)}
                            {--format=csv : Export format (csv, json, xml)}
                            {--output= : Output file path}
                            {--where=* : WHERE conditions (can be used multiple times)}
                            {--limit=1000 : Maximum number of records to export}
                            {--chunk=100 : Number of records to process at once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export data from database tables with various filtering options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');
        $format = $this->option('format');
        $output = $this->option('output');
        $whereConditions = $this->option('where');
        $limit = (int) $this->option('limit');
        $chunk = (int) $this->option('chunk');

        // If no table specified, ask user to select
        if (!$table) {
            $table = $this->choice('Which table would you like to export?', [
                'users', 'posts', 'categories', 'orders'
            ]);
        }

        $this->info("Exporting data from table: {$table}");
        $this->info("Format: {$format}");
        $this->info("Limit: {$limit} records");
        $this->info("Chunk size: {$chunk}");

        if ($whereConditions) {
            $this->info('WHERE conditions:');
            foreach ($whereConditions as $condition) {
                $this->line("  - {$condition}");
            }
        }

        // Simulate export with progress bar
        $this->info('Starting export...');
        $progressBar = $this->output->createProgressBar($limit);
        
        for ($i = 0; $i < $limit; $i += $chunk) {
            // Simulate processing chunks
            usleep(100000); // 0.1 second delay
            $progressBar->advance(min($chunk, $limit - $i));
        }
        
        $progressBar->finish();
        $this->newLine();

        $outputFile = $output ?: "export_{$table}." . $format;
        $this->info("✅ Export completed! File saved as: {$outputFile}");
        
        return Command::SUCCESS;
    }
}
