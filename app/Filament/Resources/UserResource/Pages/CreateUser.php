<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Models\History;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Mail\ConfirmRegitster;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        // Remove password_confirmation as it's not stored
        unset($data['password_confirmation']);
        
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extract roles before creating user
        $roleIds = $data['roles'] ?? [];
        unset($data['roles']);
        
        // Remove send_welcome_email from data as it's not a database field
        $sendWelcomeEmail = $data['send_welcome_email'] ?? false;
        unset($data['send_welcome_email']);
        
        // Create user
        $user = User::create($data);
        
        // Assign roles
        if (!empty($roleIds)) {
            $roles = Role::whereIn('id', $roleIds)->get();
            $user->syncRoles($roles);
        }
        
        // Log creation
        History::info()
            ->action('User Created')
            ->userId($user->id)
            ->initiator(Auth::id())
            ->message("User {$user->username} was created by admin")
            ->payload(['ip_address' => request()->ip()])
            ->write();
        
        // Send welcome email if requested
        if ($sendWelcomeEmail && $user->email) {
            try {
                Mail::to($user->email)->send(new ConfirmRegitster($user));
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email to user', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $user;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add send_welcome_email field to form data
        $data['send_welcome_email'] = false;
        return $data;
    }
}
