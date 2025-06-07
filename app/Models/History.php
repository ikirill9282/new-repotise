<?php

namespace App\Models;

use App\Enums\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;
use Symfony\Component\Mailer\SentMessage;

/**
 * user(): User
 * initer(): User
 * 
 * userStartVerify(User $user)
 * activateBackupCode(User $user, string $code): self
 * userCreated(User $user): self
 * resetUserUndefined(string $code): self
 * resetCodeExpired(?User $user, string $code): self
 * resetCodeSend(User $user): self
 * emailVerifySend(User $user): self
 * emailVerifySuccess(User $user, string $code): self
 * emailVerifyValidationError(Validator $validator): self
 * emailVerifyError(string $message, string $code): self
 * emailVerifyException(\Exception $e): self
 * 
 * success(): self
 * error(): self
 * exception(): self
 * info(): self
 * warning(): self
 * 
 * type(string $type): self
 * values(int|string|null $val = null, int|string|null $old_val = null): self
 * message(string $message): self
 * payload(array $data): self
 * action(string $action): self
 * userId(?int $id): self
 * initiator(?int $id): self
 * write(): self
 * 
 * makeValidatorMessage(Validator $validator): string
 */

class History extends Model
{
    protected $attributes = [
      'initiator' => 0,
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

    public function initer()
    {
      return $this->belongsTo(User::class, 'initialtor', 'id');
    }

    public static function userStartVerify(User $user, array $payload = [])
    {
      return static::info()
        ->action(Action::VERIFY_START)
        ->userId($user->id)
        ->message('Create verification session in stripe.')
        ->payload($payload)
        ->write()
        ;
    }

    public static function activateBackupCode(User $user, string $code)
    {
      return static::success()
        ->action(Action::BACKUP_ACTIVATE)
        ->userId($user->id)
        ->values($code)
        ->message("User backupcode [#$code] activated")
        ->write()
        ;
    }

    public static function userCreated(User $user)
    {
      return static::success()
        ->action(Action::USER_CREATED)
        ->userId($user->id)
        ->message("User registration $user->username")
        ->write()
        ;
    }

    public static function resetUserUndefined(string $code)
    {
      return static::warning()
        ->action(Action::RESET_PASSWORD)
        ->message("Undefined user with code")
        ->values($code)
        ->write()
        ;
    }

    public static function resetCodeExpired(?User $user, string $code)
    {
      return static::warning()
        ->action(Action::RESET_PASSWORD)
        ->message('Reset code is expired')
        ->userId($user?->id ?? null)
        ->values($code)
        ->write()
        ;

    }

    public static function resetCodeSend(User $user)
    {
      return static::info()
        ->action(Action::RESET_PASSWORD)
        ->userId($user->id)
        ->message("Send reset password code to user $user->id")
        ->values($user->getResetCode())
        ->write()
        ;
    }

    public static function emailVerifySend(User $user)
    {
      return static::info()
        ->action(Action::VERIFY_EMAIL)
        ->userId($user->id)
        ->message("Verification code sended to $user->email")
        ->values($user->verify->code)
        ->write()
        ;
    }

    public static function emailVerifySuccess(User $user, string $code)
    {
      return static::success()
        ->action(Action::VERIFY_EMAIL)
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
        ->action(Action::VERIFY_EMAIL)
        ->message($message)
        ->write()
        ;
    }

    public static function emailVerifyError(string $message, string $code): self
    {
      return static::error()
        ->action(Action::VERIFY_EMAIL)
        ->message($message)
        ->values($code)
        ->write()
        ;
    }

    public static function emailVerifyException(\Exception $e): self
    {
      return static::exception()
        ->message($e->getMessage())
        ->action(Action::VERIFY_EMAIL)
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

    public function initiator(?int $id): self
    {
      $this->initiator = $id;
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
