<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

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

    public static function emailVerifySuccess(int $user_id, array $payload = [])
    {
      return static::success()
        ->action('Verify Email')
        ->userId($user_id)
        ->payload($payload)
        ->write()
        ;
    }

    public static function emailVerifyValidationError(Validator $validator)
    {
      $message = static::makeValidatorMessage($validator);
      return static::emailVerifyError($message);
    }

    public static function emailVerifyError(string $message, array $payload = []): self
    {
      return static::error()
        ->action('Verify Email')
        ->message($message)
        ->payload($payload)
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

    public function type(string $type): self
    {
      $this->type = $type;

      return $this;
    }

    public function values(int|string|null $val = null, int|string|null $old_val = null): self
    {
      $this->val = $val;
      $this->old_val = $old_val;

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

    public function userId(int $id): self
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
