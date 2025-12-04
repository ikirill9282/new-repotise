<?php

namespace App\Livewire\Modals;

use App\Helpers\CustomEncrypt;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Report as ReportModel;
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
        'reason' => null,
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
        $this->form['reason'] = null;

        $modelId = CustomEncrypt::getId($this->modelHash);

        if (!$modelId) {
            $this->handleMissingTarget();
            return;
        }

        $query = match ($this->resource) {
            'review' => Review::query(),
            'comment' => Comment::query()->with(['author', 'author.options']),
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

        // Ensure author is loaded for comments
        if ($this->resource === 'comment' && $this->record && !$this->record->relationLoaded('author')) {
            $this->record->load('author', 'author.options');
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

        // Different validation for comments vs articles
        if ($this->resource === 'comment') {
            // For comments, require reason checkbox
            $validator = Validator::make(
                $this->form,
                [
                    'reason' => 'required|string|in:spam,offensive,inappropriate',
                ],
                [
                    'reason.required' => 'Please select a reason for reporting this comment.',
                    'reason.in' => 'Please select a valid reason.',
                ]
            );
        } else {
            // For articles/reviews, require text message
            $message = $this->form['message'] ?? null;
            if (is_string($message)) {
                $message = trim($message);
                $this->form['message'] = $message;
            }

            if (empty($message)) {
                $this->form['message'] = '';
            }

            $validator = Validator::make(
                $this->form,
                [
                    'message' => 'required|string|min:10|max:200',
                ],
                [
                    'message.required' => 'Please describe the error you found. Your message must be at least 10 characters long.',
                    'message.min' => 'Please provide a bit more detail so we can review it properly. Your message must be at least 10 characters long.',
                    'message.max' => 'Your report is too long. Please shorten it to 200 characters.',
                ]
            );
        }

        if ($validator->fails()) {
            $this->submitted = false;
            $this->failed = false; // Don't show error screen, show validation errors instead
            
            // Add validation errors to the form
            foreach ($validator->errors()->all() as $error) {
                if ($this->resource === 'comment') {
                    $this->addError('form.reason', $validator->errors()->first('reason') ?: $error);
                } else {
                    $this->addError('form.message', $validator->errors()->first('message') ?: $error);
                }
            }
            
            return;
        }

        $data = $validator->validated();

        try {
            // Prepare report data
            $reportData = [
                'user_id' => Auth::id(),
            ];

            if ($this->resource === 'comment') {
                // For comments, map reason to readable format and save both reason and message
                $reasonMap = [
                    'spam' => ReportModel::REASON_SPAM_OR_SCAM,
                    'offensive' => ReportModel::REASON_OFFENSIVE,
                    'inappropriate' => ReportModel::REASON_INAPPROPRIATE,
                ];
                
                $reportData['reason'] = $reasonMap[$data['reason']] ?? $data['reason'];
                $reportData['message'] = $reportData['reason'];
                $reportData['type'] = ReportModel::TYPE_COMPLAINT;
            } else {
                // For articles/reviews, use message
                $reportData['message'] = $data['message'];
                $reportData['type'] = ReportModel::TYPE_CONTENT_ERROR;
            }

            $reportData['status'] = ReportModel::STATUS_NEW;

            $this->record->reports()->create($reportData);
        } catch (Throwable $exception) {
            report($exception);
            $this->failed = true;
            $this->dispatch('toastError', ['message' => 'We couldn’t submit your report. Please try again later.']);
            return;
        }

        $this->submitted = true;
        $this->failed = false;
        
        $this->reset('form');
        $this->resetValidation();
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
