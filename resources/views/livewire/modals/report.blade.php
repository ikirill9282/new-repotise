<div class="w-full space-y-4">
  <style>
    input[type="radio"][name="report_reason"] {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(0, 0, 0, 0.3);
      border-radius: 4px;
      position: relative;
      cursor: pointer;
    }
    input[type="radio"][name="report_reason"]:checked {
      background-color: #FF7F66;
      border-color: #FF7F66;
    }
    input[type="radio"][name="report_reason"]:checked::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: 14px;
      font-weight: bold;
    }
  </style>
  @if ($submitted)
    <div class="text-center space-y-6">
      <div class="text-2xl font-semibold text-black">Thanks! We'll look into it.</div>
      <div class="flex justify-center">
        @include('icons.success', ['width' => 144, 'height' => 144])
      </div>
      <div class="flex justify-center">
        <button type="button" wire:click.prevent="$dispatch('closeModal')" class=" main-btn" style="background-color: #FF7F66;">
          Done
        </button>
      </div>
    </div>
  @elseif($failed)
    <div class="text-center space-y-4">
      <div class="text-2xl font-semibold text-red-500">Error</div>
      <p class="text-gray max-w-lg mx-auto mb-6">
        @if($resource === 'comment')
          @if(empty($form['reason']))
            Please select a reason for reporting this comment.
          @else
            Something went wrong while sending your report. Please try again in a moment.
          @endif
        @else
          @if(empty($form['message']) || mb_strlen(trim($form['message'] ?? ''), 'UTF-8') < 10)
            Please describe the error you found. Your message must be at least 10 characters long.
          @else
            Something went wrong while sending your message. Please try again in a moment.
          @endif
        @endif
      </p>
      <div class="flex justify-center gap-3">
        <x-btn class="!px-6" wire:click.prevent="$set('failed', false)">Back</x-btn>
        <x-btn class="!px-6" wire:click.prevent="$dispatch('closeModal')">Close</x-btn>
      </div>
    </div>
  @else
    @if($resource === 'comment')
      {{-- Comment Report Form with Checkboxes --}}
      <div class="flex flex-col gap-6">
        {{-- Header --}}
        <div class="text-2xl font-semibold text-black mb-2 text-center">Report this Comment</div>
        
        {{-- Comment Info --}}
        @if($record)
          @php
            $author = $record->author ?? null;
            $avatar = null;
            if ($author && $author->options) {
              $avatar = $author->options->avatar ?? null;
            }
            $avatarUrl = $avatar ? asset($avatar) : asset('assets/img/default-avatar.png');
            $username = $author->username ?? 'Unknown';
            $commentText = strip_tags($record->text ?? '');
            $isEdited = $record->updated_at && $record->updated_at->ne($record->created_at);
          @endphp
          <div class="flex gap-3 pb-4 border-b border-gray/20">
            <img src="{{ $avatarUrl }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <span class="font-semibold text-black">{{ $username }}</span>
                <span class="text-sm text-gray">{{ $record->created_at ? $record->created_at->format('m.d.Y') : '' }}</span>
              </div>
              <p class="text-sm text-gray mb-2">{{ \Illuminate\Support\Str::limit($commentText, 100) }}</p>
              @if($isEdited)
                <span class="text-xs text-gray">Edited</span>
              @endif
            </div>
          </div>
        @endif

        {{-- Report Form --}}
        <form wire:submit.prevent="submit" class="flex flex-col gap-4">
          <div class="flex flex-col gap-2">
            <label class="text-base font-semibold text-black text-center mt-4">Tell us what's wrong with this comment</label>
            
            <div class="flex flex-row gap-3 w-full justify-center items-center">
              <label class="!flex !flex-row !items-center gap-2 p-3 rounded border @error('form.reason') border-red-500 @else border-gray/30 @enderror hover:border-active cursor-pointer transition w-[32%]">
                <span class="text-sm text-black">Spam or Scam</span>
                <input type="radio" wire:model="form.reason" value="spam" name="report_reason">
              </label>
              
              <label class="!flex !flex-row !items-center gap-2 p-3 rounded border @error('form.reason') border-red-500 @else border-gray/30 @enderror hover:border-active cursor-pointer transition w-[32%]">
                <span class="text-sm text-black">Offensive or abusive</span>
                <input type="radio" wire:model="form.reason" value="offensive" name="report_reason">
              </label>
              
              <label class="!flex !flex-row !items-center gap-2 p-3 rounded border @error('form.reason') border-red-500 @else border-gray/30 @enderror hover:border-active cursor-pointer transition w-[32%]">
                <span class="text-sm text-black">Inappropriate content</span>
                <input type="radio" wire:model="form.reason" value="inappropriate" name="report_reason">
              </label>
            </div>
            
            @error('form.reason')
              <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="flex items-center justify-center gap-2 pt-2">
            <button type="submit" class="w-full main-btn" style="background-color: #FF7F66;" wire:loading.attr="disabled">
              <span wire:loading.remove>Submit Report</span>
              <span wire:loading>Submitting...</span>
            </button>
          </div>
        </form>
      </div>
    @else
      {{-- Article/Review Report Form with Textarea --}}
      <form wire:submit.prevent="submit" class="flex flex-col gap-4">
        <div class="flex flex-col gap-2">
          <label class="text-base font-semibold text-black title-popup-question" for="report-message">Found an error? Let us know!</label>
          <div class="relative">
            <textarea
              id="report-message"
              wire:model="form.message"
              rows="4"
              maxlength="200"
              class="w-full rounded border border-gray/50 p-3 shadow-sm focus:border-active focus:ring-0 resize-y min-h-[150px]"
              placeholder="Briefly describe the error here..."
              x-on:input="document.getElementById('char-count-text').textContent = $el.value.length"
            ></textarea>
            <div class="absolute bottom-3 right-3 text-sm text-gray pointer-events-none">
              <span id="char-count-text">{{ mb_strlen($form['message'] ?? '', 'UTF-8') }}</span>/200
            </div>
          </div>
          @error('form.message')
            <div class="text-sm text-red-500">{{ $message }}</div>
          @enderror
        </div>

        <div class="popUp__donateThank-buttons flex items-center justify-center gap-2">
          <button type="submit" class="popUp__edit-contact-btn main-btn" wire:loading.attr="disabled">
            <span wire:loading.remove>Submit</span>
            <span wire:loading>Submitting...</span>
          </button>
        </div>
      </form>
    @endif
  @endif
</div>
