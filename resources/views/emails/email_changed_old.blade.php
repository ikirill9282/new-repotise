@php
  /** @var \App\Models\User $user */
  /** @var string $newEmail */
@endphp

Hi {{ $user->getName() }},

The email address for your TrekGuider account was recently changed to {{ $newEmail }}.

If this was you, no action is needed.

If you didn't make this change, please contact our support team immediately so we can help secure your account.

Stay safe,
The TrekGuider Team

