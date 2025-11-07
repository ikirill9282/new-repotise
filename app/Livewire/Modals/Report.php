<?php

namespace App\Livewire\Modals;

use App\Helpers\CustomEncrypt;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

class Report extends Component
{
    public string $modelHash;

    public array $form = [
        'message' => null,
    ];

    public string $resource;

    public ?Model $record = null;

    public string $context = '';

    public bool $submitted = false;

    public bool $failed = false;

    public function mount($model, $resource): void
    {
        $this->modelHash = (string) $model;
        $this->resource = (string) $resource;
        $this->submitted = false;
        $this->failed = false;
        $this->form['message'] = null;

        $modelId = CustomEncrypt::getId($this->modelHash);

        if (!$modelId) {
            $this->handleMissingTarget();
            return;
        }

        $query = match ($this->resource) {
            'review' => Review::query(),
            'comment' => Comment::query(),
            'article' => Article::query(),
            default => null,
        };

        if (!$query) {
            $this->handleMissingTarget();
            return;
        }

        $this->record = $query->find($modelId);

        if (!$this->record) {
            $this->handleMissingTarget();
            return;
        }

        $this->context = $this->buildContext();
    }

    public function submit(): void
    {
        if (!$this->record) {
            $this->handleMissingTarget();
            return;
        }

        if (!Auth::check()) {
            $this->dispatch('closeModal');
            $this->dispatch('openModal', 'auth');
            return;
        }

        if (is_string($this->form['message'])) {
            $this->form['message'] = trim($this->form['message']);
        }

        $validator = Validator::make(
            $this->form,
            [
                'message' => 'required|string|min:10|max:1000',
            ],
            [
                'message.required' => 'Please describe the issue you spotted.',
                'message.min' => 'Please provide a bit more detail so we can review it properly.',
                'message.max' => 'Your report is too long. Please shorten it to 1000 characters.',
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $data = $validator->validated();

        try {
            $this->record->reports()->create([
                'user_id' => Auth::id(),
                'message' => $data['message'],
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $this->failed = true;
            $this->dispatch('toastError', ['message' => 'We couldn’t submit your report. Please try again later.']);
            return;
        }

        $this->reset('form');
        $this->resetValidation();

        $this->submitted = true;
        $this->failed = false;

        $this->dispatch('toastSuccess', ['message' => 'Thanks for your report! We will review it shortly.']);
    }

    public function render()
    {
        return view('livewire.modals.report');
    }

    protected function buildContext(): string
    {
        if (!$this->record) {
            return '';
        }

        return match ($this->resource) {
            'article' => 'Reporting article: ' . ($this->record->title ?? ''),
            default => Str::limit(
                strip_tags(
                    (string) ($this->record->text ?? $this->record->message ?? '')
                ),
                160
            ),
        };
    }

    protected function handleMissingTarget(): void
    {
        $this->record = null;
        $this->submitted = false;
        $this->failed = true;
        $this->dispatch('toastError', ['message' => 'We couldn’t find that item. Please refresh and try again.']);
    }
}
