<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignCreatorRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-creator-role {email? : Email address or domain pattern (e.g., @trekguider.com)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign creator role to user(s) by email domain';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $emailPattern = $this->argument('email') ?? '@trekguider.com';
        
        // Get creator role
        $creatorRole = Role::firstOrCreate(
            ['name' => 'creator'],
            ['name' => 'creator', 'title' => 'Creator']
        );

        // Find users by email pattern
        if (str_starts_with($emailPattern, '@')) {
            // Domain pattern - search for emails ending with the domain
            $users = User::where('email', 'like', '%' . $emailPattern)->get();
        } else {
            // Specific email
            $users = User::where('email', $emailPattern)->get();
        }
        
        // Debug: show all users with trekguider in email
        if ($users->isEmpty() && str_contains($emailPattern, 'trekguider')) {
            $this->warn("No users found. Searching for any users with 'trekguider' in email...");
            $allTrekguiderUsers = User::where('email', 'like', '%trekguider%')->get();
            if ($allTrekguiderUsers->isNotEmpty()) {
                $this->info("Found users with 'trekguider' in email:");
                foreach ($allTrekguiderUsers as $u) {
                    $this->line("  - {$u->email} (ID: {$u->id})");
                }
            }
        }

        if ($users->isEmpty()) {
            $this->error("No users found with email pattern: {$emailPattern}");
            return Command::FAILURE;
        }

        $this->info("Found {$users->count()} user(s) with email pattern: {$emailPattern}");

        $assigned = 0;
        foreach ($users as $user) {
            if (!$user->hasRole('creator')) {
                $user->assignRole($creatorRole);
                $this->line("✓ Assigned creator role to: {$user->email} (ID: {$user->id})");
                $assigned++;
            } else {
                $this->line("- User {$user->email} already has creator role");
            }
        }

        $this->info("\n✓ Successfully assigned creator role to {$assigned} user(s)");
        return Command::SUCCESS;
    }
}
