<?php

/**
 * Script to assign creator role to users with @trekguider.com email
 * 
 * Usage:
 * 1. Run via tinker: php artisan tinker
 *    Then paste the code below
 * 
 * 2. Or run directly: php assign_creator_role.php (if database is accessible)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Get or create creator role
$creatorRole = Role::firstOrCreate(
    ['name' => 'creator'],
    ['name' => 'creator', 'title' => 'Creator']
);

// Find all users with @trekguider.com email
$users = User::where('email', 'like', '%@trekguider.com')->get();

if ($users->isEmpty()) {
    echo "No users found with @trekguider.com email\n";
    exit(1);
}

echo "Found {$users->count()} user(s) with @trekguider.com email\n\n";

$assigned = 0;
foreach ($users as $user) {
    if (!$user->hasRole('creator')) {
        $user->assignRole($creatorRole);
        echo "✓ Assigned creator role to: {$user->email} (ID: {$user->id})\n";
        $assigned++;
    } else {
        echo "- User {$user->email} already has creator role\n";
    }
}

echo "\n✓ Successfully assigned creator role to {$assigned} user(s)\n";

