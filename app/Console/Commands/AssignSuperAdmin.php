<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-superadmin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign super-admin role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return Command::FAILURE;
        }

        // Remove all existing roles
        $user->syncRoles([]);

        // Assign super-admin role
        $user->assignRole('super-admin');

        $this->info("✓ Successfully assigned 'super-admin' role to {$user->name} ({$user->email})");

        // Show current roles
        $currentRoles = $user->roles->pluck('name')->implode(', ');
        $this->info("Current roles: {$currentRoles}");

        return Command::SUCCESS;
    }
}
