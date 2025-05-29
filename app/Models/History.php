<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;
use Symfony\Component\Mailer\SentMessage;

class History extends Model
{
    protected $attributes = [
      'user_id' => null,
      'action' => null,
      'type' => null,
      'message' => null,
      'value' => null,
      'old_value' => null,
      'payload' => null,
    ];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public static function resetUserUndefined(string $code)
    {
      return static::warning()
        ->message("Undefined user with code")
        ->values($code)
        ->write()
        ;
    }

    public static function resetCodeExpired(?User $user, string $code)
    {
      return static::warning()
        ->action('Reset Code')
        ->message('Reset code is expired')
        ->userId($user?->id ?? null)
        ->values($code)
        ->write()
        ;

    }

    public static function resetCodeSend(User $user)
    {
      return static::info()
        ->action('Reset Code')
        ->userId($user->id)
        ->message("Send reset code to user $user->id")
        ->values($user->getResetCode())
        ->write()
        ;
    }

    public static function emailVerifySend(User $user)
    {
      return static::info()
        ->action('Verify Email')
        ->userId($user->id)
        ->message("Verification code sended to $user->email")
        ->values($user->verify->code)
        ->write()
        ;
    }

    public static function emailVerifySuccess(User $user, string $code)
    {
      return static::success()
        ->action('Verify Email')
        ->userId($user->id)
        ->message("Email verification sucess $user->username")
        ->values($code)
        ->write()
        ;
    }

    public static function emailVerifyValidationError(Validator $validator)
    {
      $message = static::makeValidatorMessage($validator);
      return static::error()
        ->action('Verify Email')
        ->message($message)
        ->write()
        ;
    }

    public static function emailVerifyError(string $message, string $code): self
    {
      return static::error()
        ->action('Verify Email')
        ->message($message)
        ->values($code)
        ->write()
        ;
    }

    public static function emailVerifyException(\Exception $e): self
    {
      return static::exception()
        ->message($e->getMessage())
        ->action('Verify Email')
        ->payload([
          'file' => $e->getFile(),
          'line' => $e->getLine(),
          'code' => $e->getCode(),
        ])
        ->write()
        ;
    }

    public static function success(): self
    {
      return (new static())->type('success');
    }

    public static function error(): self
    {
      return (new static())->type('error');
    }

    public static function exception(): self
    {
      return (new static())->type('exception');
    }

    public static function info(): self
    {
      return (new static())->type('info');
    }

    public static function warning(): self
    {
      return (new static())->type('warning');
    }

    public function type(string $type): self
    {
      $this->type = $type;

      return $this;
    }

    public function values(int|string|null $val = null, int|string|null $old_val = null): self
    {
      $this->value = $val;
      $this->old_value = $old_val;

      return $this;
    }

    public function message(string $message): self
    {
      $this->message = $message;

      return $this;
    }

    public function payload(array $data): self
    {
      $this->payload = json_encode($data);

      return $this;
    }

    public function action(string $action): self
    {
      $this->action = $action;

      return $this;
    }

    public function userId(?int $id): self
    {
      $this->user_id = $id;
      return $this;
    }

    public function write(): self
    {
      $this->save();
      $this->refresh();

      return $this;
    }

    protected static function makeValidatorMessage(Validator $validator)
    {
      return collect($validator->errors()->getMessages())
        ->map(fn($message) => is_array($message) ? implode(', ', $message) : $message)
        ->implode("\n");
    }
}
