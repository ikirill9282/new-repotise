<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\History;

class ResetFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
      protected ?User $user,
      protected string $code,
      protected string $reason,
    )
    {
        $reason = match ($this->reason) {
          'user' => 'Undefined user.',
          'code' => 'Code is expired.',
          'invalid' => 'Invalid code',
        };
        
        Log::channel('email')->warning("Reset Password failed. $reason", ['code' => $this->code]);

        match ($this->reason) {
          'user' => History::resetUserUndefined($this->code),
          'code' => History::resetCodeExpired($user, $this->code),
          'invalid' => History::resetCodeInvalid($user, $this->code),
        };
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
