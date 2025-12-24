<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignSellerRoleToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:assign-seller-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign creator (seller) role to admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Assigning creator (seller) role to admin user...');
        
        // Get or create creator role (seller role)
        $creatorRole = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'creator'],
            ['name' => 'creator', 'title' => 'Creator']
        );
        
        // Find admin user by role
        $admin = \App\Models\User::role(['super-admin', 'admin'])->first();
        
        if (!$admin) {
            // Try to find by email from env
            $adminEmail = env('ADMIN_MAIL');
            if ($adminEmail) {
                $admin = \App\Models\User::where('email', $adminEmail)->first();
            }
        }
        
        if (!$admin) {
            $this->error('Admin user not found. Please check:');
            $this->error('   - User with \'super-admin\' or \'admin\' role exists');
            $this->error('   - Or ADMIN_MAIL in .env file is set correctly');
            return 1;
        }
        
        $this->info("Found admin user: {$admin->email} (ID: {$admin->id})");
        $this->info("Current roles: " . implode(', ', $admin->getRoleNames()->toArray()));
        
        if (!$admin->hasRole('creator')) {
            $admin->assignRole($creatorRole);
            $this->info("✓ Successfully assigned creator (seller) role to admin: {$admin->email}");
            $this->info("New roles: " . implode(', ', $admin->fresh()->getRoleNames()->toArray()));
        } else {
            $this->info("✓ Admin already has creator (seller) role");
        }
        
        return 0;
    }
}
