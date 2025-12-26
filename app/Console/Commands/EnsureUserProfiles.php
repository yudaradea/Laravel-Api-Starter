<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EnsureUserProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:ensure-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all users have profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking users without profiles...');

        $usersWithoutProfiles = User::doesntHave('profile')->get();

        if ($usersWithoutProfiles->isEmpty()) {
            $this->info('All users already have profiles.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($usersWithoutProfiles as $user) {
            $user->profile()->create([
                'phone' => null,
                'address' => null,
                'bio' => null,
                'avatar' => null,
            ]);
            $count++;
            $this->info("Created profile for user: {$user->name} (ID: {$user->id})");
        }

        $this->info("Successfully created {$count} profile(s).");
        return Command::SUCCESS;
    }
}
