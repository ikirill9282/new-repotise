<?php

namespace App\Livewire\Profile;

use App\Models\Article;
use App\Models\Country;
use App\Models\Product;
use App\Models\User;
use App\Models\UserOptions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public ?User $user = null;

    public array $social = [];

    public array $socialLabels = [];

    public $countries = [];

    public string $full_name = '';

    public ?int $country_id = null;

    public string $description = '';

    public bool $collaboration = false;

    public ?string $contact = null;

    public ?string $contact2 = null;

    public array $stats = [];

    public $products;

    public $articles;

    protected function rules(): array
    {
        $socialRules = [];
        foreach (array_keys($this->socialLabels) as $key) {
            $socialRules["social.$key"] = ['nullable', 'string', 'max:255'];
        }

        return array_merge([
            'full_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'collaboration' => ['boolean'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'contact' => ['nullable', 'string', 'max:255'],
            'contact2' => ['nullable', 'string', 'max:255'],
        ], $socialRules);
    }

    public function mount(): void
    {
        $this->user = Auth::user();

        if (!$this->user) {
            abort(403);
        }

        $this->socialLabels = UserOptions::getSocialLables();

        $this->countries = Country::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->hydrateForm();

        $this->loadSidebarData();
    }

    protected function hydrateForm(): void
    {
        $options = $this->user->options()->firstOrCreate([]);

        $this->full_name = $options->full_name ?? $this->user->name ?? '';
        $this->description = $options->description ?? '';
        $this->collaboration = (bool) ($options->collaboration ?? false);
        $this->country_id = $options->country_id;
        $this->contact = $options->contact;
        $this->contact2 = $options->contact2;

        foreach (array_keys($this->socialLabels) as $key) {
            $this->social[$key] = $options->{$key} ?? '';
        }
    }

    protected function loadSidebarData(): void
    {
        $this->stats = [
            'followers' => $this->user->followers()->count(),
            'products' => $this->user->products()->count(),
            'articles' => $this->user->articles()->count(),
            'donations' => $this->user->funds()
                ->where('group', 'donation')
                ->sum('sum'),
        ];

        $this->products = $this->user->products()
            ->latest()
            ->with('preview')
            ->take(6)
            ->get();

        $this->articles = $this->user->articles()
            ->latest()
            ->with('preview')
            ->take(6)
            ->get();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'full_name' => $this->full_name,
            'description' => $this->description,
            'collaboration' => $this->collaboration,
            'country_id' => $this->country_id,
            'contact' => $this->contact,
            'contact2' => $this->contact2,
        ];

        foreach ($this->social as $key => $value) {
            $data[$key] = $value;
        }

        $this->user->options()->updateOrCreate(
            ['user_id' => $this->user->id],
            $data
        );

        if (!empty($this->full_name)) {
            $this->user->name = $this->full_name;
            $this->user->save();
        }

        $this->loadSidebarData();

        session()->flash('status', 'Profile updated successfully.');
    }

    public function cancel(): void
    {
        $this->hydrateForm();
        session()->flash('status', 'Changes reverted.');
    }

    public function render()
    {
        return view('livewire.profile.edit', [
            'countries' => $this->countries,
            'products' => $this->products,
            'articles' => $this->articles,
        ]);
    }
}
