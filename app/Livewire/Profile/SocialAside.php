<?php

namespace App\Livewire\Profile;

use App\Models\User;
use App\Models\UserOptions;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Livewire\Component;

class SocialAside extends Component
{
    public int $userId;

    public bool $owner = false;

    public array $icons = [];

    public array $labels = [];

    public array $social = [];

    public array $visibility = [];

    public function mount(int $userId, bool $owner = false): void
    {
        $this->userId = $userId;
        $this->owner = $owner;

        $this->hydrateState();
    }

    #[On('resetPage')]
    public function hydrateState(): void
    {
        $user = $this->getUser();
        $options = $user->options()->firstOrCreate([]);

        $this->icons = UserOptions::getSocialIcons();
        $this->labels = UserOptions::getSocialLables();
        $this->social = $options->getSocial();
        $this->visibility = $options->getSocialVisibility();
    }

    public function toggleAll(bool $state): void
    {
        if (!$this->owner) {
            return;
        }

        foreach ($this->visibility as $key => $value) {
            if (empty($this->social[$key])) {
                $this->visibility[$key] = false;
                continue;
            }

            $this->visibility[$key] = $this->normalizeBool($state);
        }

        $this->persistVisibility();
    }

    public function getVisibleSocialsProperty(): array
    {
        return collect($this->social)
            ->filter(fn ($url, $key) => $this->isKnownSocialKey($key))
            ->filter(fn ($url) => !empty($url))
            ->filter(fn ($url, $key) => (bool) ($this->visibility[$key] ?? false))
            ->toArray();
    }

    public function render()
    {
        return view('livewire.profile.social-aside', [
            'visibleSocials' => $this->visibleSocials,
        ]);
    }

    protected function getUser(): User
    {
        return User::with('options')->findOrFail($this->userId);
    }

    protected function persistVisibility(): void
    {
        $user = $this->getUser();
        $options = $user->options()->firstOrCreate([]);

        $cleaned = Arr::only($this->visibility, array_keys($this->icons));

        foreach ($cleaned as $key => &$value) {
            $value = $this->normalizeBool($value) && !empty($this->social[$key]);
        }
        unset($value);

        $options->forceFill(['social_visibility' => $cleaned])->save();

        $this->visibility = $options->fresh()->getSocialVisibility();

        $this->dispatch('toastSuccess', ['message' => 'Social visibility updated.']);
    }

    protected function isKnownSocialKey(string $key): bool
    {
        return array_key_exists($key, $this->icons);
    }

    public function setVisibility(string $key, mixed $value): void
    {
        if (!$this->owner) {
            $this->visibility[$key] = (bool) ($this->visibility[$key] ?? false);
            return;
        }

        if (!$this->isKnownSocialKey($key)) {
            unset($this->visibility[$key]);
            return;
        }

        if (empty($this->social[$key])) {
            $this->visibility[$key] = false;
            return;
        }

        $this->visibility[$key] = $this->normalizeBool($value);

        $this->persistVisibility();
    }

    protected function normalizeBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $normalized = strtolower($value);

            if (in_array($normalized, ['true', '1', 'yes', 'on'], true)) {
                return true;
            }

            if (in_array($normalized, ['false', '0', 'no', 'off', ''], true)) {
                return false;
            }
        }

        return (bool) $value;
    }
}

