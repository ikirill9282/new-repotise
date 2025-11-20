<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Change Password Section --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header">
                <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">Change Password</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your administrator password</p>
            </div>
            
            <div class="mt-6">
                <form wire:submit="changePassword">
                    {{ $this->passwordForm }}
                    
                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit">
                            Change Password
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Two-Factor Authentication Section --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header">
                <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">Two-Factor Authentication</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Status: 
                    <span class="font-semibold {{ $this->twofaEnabled ? 'text-success-600 dark:text-success-400' : 'text-gray-500' }}">
                        {{ $this->twofaEnabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </p>
            </div>
            
            <div class="mt-6 space-y-4">
                @if(!$this->twofaEnabled)
                    @if(!$this->twofaSecret)
                        <x-filament::button wire:click="enableTwoFactor" color="primary">
                            Enable 2FA
                        </x-filament::button>
                    @else
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Scan this QR code with your authenticator app:</p>
                                <div class="inline-block p-4 bg-white border border-gray-300 rounded">
                                    <img src="{{ $this->twofaQrCode }}" alt="2FA QR Code" class="w-48 h-48">
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Or enter this secret key manually:</p>
                                <code class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-sm">{{ $this->twofaSecret }}</code>
                            </div>
                            <div>
                                <x-filament::input 
                                    wire:model="twofaCode" 
                                    type="text" 
                                    placeholder="Enter verification code"
                                    class="max-w-xs"
                                />
                                <x-filament::button 
                                    wire:click="confirmTwoFactor" 
                                    color="success"
                                    class="mt-2"
                                >
                                    Confirm & Enable
                                </x-filament::button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Two-factor authentication is currently enabled for your account.</p>
                        <div>
                            <x-filament::input 
                                wire:model="twofaDisableCode" 
                                type="text" 
                                placeholder="Enter verification code to disable"
                                class="max-w-xs"
                            />
                            <x-filament::button 
                                wire:click="disableTwoFactor" 
                                color="danger"
                                class="mt-2"
                            >
                                Disable 2FA
                            </x-filament::button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Blocked Users Section --}}
        @php
            $blockedUsers = \App\Models\User::whereNotNull('login_locked_until')
                ->where('login_locked_until', '>', now())
                ->orWhere(function($q) {
                    $q->where('failed_login_attempts', '>=', 5)
                      ->whereNotNull('last_failed_login_at')
                      ->where('last_failed_login_at', '>', now()->subMinutes(15));
                })
                ->get();
        @endphp
        
        @if($blockedUsers->isNotEmpty())
            <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header">
                    <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">Blocked Users</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Users locked due to multiple failed login attempts</p>
                </div>
                
                <div class="mt-6">
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-white/5">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">User</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Email</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Locked Until</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Failed Attempts</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @foreach($blockedUsers as $user)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-950 dark:text-white">{{ $user->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->login_locked_until ? $user->login_locked_until->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $user->failed_login_attempts }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <x-filament::button 
                                                wire:click="unblockUser({{ $user->id }})" 
                                                size="sm"
                                                color="success"
                                            >
                                                Unblock
                                            </x-filament::button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Login History Table --}}
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header">
                <h3 class="text-lg font-semibold leading-6 text-gray-950 dark:text-white">{{ $this->getTableHeading() }}</h3>
                @if($this->getTableDescription())
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $this->getTableDescription() }}</p>
                @endif
            </div>
            
            <div class="mt-6">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
