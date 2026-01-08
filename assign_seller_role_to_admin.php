<?php

/**
 * Script to assign creator (seller) role to admin user
 * 
 * Usage:
 * php assign_seller_role_to_admin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Get or create creator role (seller role)
$creatorRole = Role::firstOrCreate(
    ['name' => 'creator'],
    ['name' => 'creator', 'title' => 'Creator']
);

// Find admin user by role
$admin = User::role(['super-admin', 'admin'])->first();

if (!$admin) {
    // Try to find by email from env
    $adminEmail = env('ADMIN_MAIL');
    if ($adminEmail) {
        $admin = User::where('email', $adminEmail)->first();
    }
}

if (!$admin) {
    echo "❌ Admin user not found. Please check:\n";
    echo "   - User with 'super-admin' or 'admin' role exists\n";
    echo "   - Or ADMIN_MAIL in .env file is set correctly\n";
    exit(1);
}

echo "Found admin user: {$admin->email} (ID: {$admin->id})\n";
echo "Current roles: " . implode(', ', $admin->getRoleNames()->toArray()) . "\n\n";

if (!$admin->hasRole('creator')) {
    $admin->assignRole($creatorRole);
    echo "✓ Successfully assigned creator (seller) role to admin: {$admin->email}\n";
    echo "New roles: " . implode(', ', $admin->fresh()->getRoleNames()->toArray()) . "\n";
} else {
    echo "✓ Admin already has creator (seller) role\n";
}





