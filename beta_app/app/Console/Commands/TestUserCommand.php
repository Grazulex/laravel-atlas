<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create 
                            {name : The name of the user}
                            {email : The email of the user}
                            {--admin : Make the user an admin}
                            {--role=guest : The role of the user}
                            {--force : Force creation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with various options and arguments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $isAdmin = $this->option('admin');
        $role = $this->option('role');
        $force = $this->option('force');

        if (!$force && !$this->confirm("Create user {$name} with email {$email}?")) {
            $this->info('User creation cancelled.');
            return Command::FAILURE;
        }

        $this->info("Creating user: {$name}");
        $this->info("Email: {$email}");
        $this->info("Role: {$role}");
        
        if ($isAdmin) {
            $this->warn('User will have admin privileges');
        }

        // Simulate user creation
        $this->info('✅ User created successfully!');
        
        return Command::SUCCESS;
    }
}
