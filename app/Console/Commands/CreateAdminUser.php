<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user 
                            {email : Email address for admin user}
                            {--password= : Password (if not provided, will be generated)}
                            {--name=Admin : Name for admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user with admin and super-admin roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->option('name');
        $password = $this->option('password');

        // Проверяем, существует ли пользователь
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->warn("User with email {$email} already exists (ID: {$user->id})");
            
            if (!$this->confirm('Do you want to assign admin roles to this user?')) {
                return Command::FAILURE;
            }
        } else {
            // Генерируем пароль, если не указан
            if (!$password) {
                $password = $this->generatePassword();
                $this->info("Generated password: {$password}");
            }

            // Создаем пользователя
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make($password),
            ]);

            $this->info("✓ Created user: {$email} (ID: {$user->id})");
        }

        // Создаем роли, если их нет
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['name' => 'super-admin', 'title' => 'Admin']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['name' => 'admin', 'title' => 'Admin']
        );

        // Назначаем роли
        if (!$user->hasRole('super-admin')) {
            $user->assignRole($superAdminRole);
            $this->info("✓ Assigned super-admin role");
        } else {
            $this->line("- User already has super-admin role");
        }

        if (!$user->hasRole('admin')) {
            $user->assignRole($adminRole);
            $this->info("✓ Assigned admin role");
        } else {
            $this->line("- User already has admin role");
        }

        $this->newLine();
        $this->info("✓ Admin user is ready!");
        $this->info("Email: {$email}");
        if (isset($password)) {
            $this->info("Password: {$password}");
        }
        $this->info("You can now login at: /admin");

        return Command::SUCCESS;
    }

    /**
     * Generate a secure random password
     */
    private function generatePassword(): string
    {
        $length = 12;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
    }
}

