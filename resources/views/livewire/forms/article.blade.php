<div>
  <div class="max-w-4xl">
    
    {{-- CONTENT --}}
    <h2 class="!font-bold !text-xl !mb-10">Create New Article</h2>
    <div class="flex flex-col justify-start items-stretch !mb-10">
      <div class="flex flex-col justify-start items-stretch !gap-6">
        
        <x-form.input wire:model="fields.title" name="title" label="Article Title" placeholder="Enter your article title here..." />
    
        <div wire:ignore class="">
          <x-form.text-editor wire:model="fields.text" name="text" label="Article Content" placeholder="Start writing your article here..."></x-form.text-editor>
        </div>

        <div class="">
          <x-form.chips entangle="tags" source="tags" name="tags" label="Tags" placeholder="Search or create tags...(Up to 5)" tooltipText="Enter relevant keywords to categorize your article. Use up to 5 tags. Tags help readers find your article and improve searchability." />
        </div>
        
        @if(!$this->fields['published_at'] ?? null)
          <div class="">
            <x-form.datepicker wire:model="fields.scheduled_at" label="Publish Date" placeholder="Schedule a publication date MM/DD/YEAR" />
          </div>
        @endif
      </div>
    </div>

    {{-- BANNER --}}
    <h2 class="!font-bold !text-xl !mb-10">Featured Image</h2>
    <div class="banner-block relative !mb-10">
      
      <div wire:loading class="absolute w-full h-full bg-light/50 z-150">
        <x-loader width="60" height="60" />
      </div>

      <div class="max-w-sm !mb-6">
        @if($this->banner)
          <img src="{{ $this->banner->temporaryUrl() }}" alt="Banner">
        @elseif ($this->bannerPath)
          <img src="{{ $this->bannerPath ?? '' }}" alt="Banner">
        @endif
      </div>

      <div class="flex flex-col justify-start items-stretch relative">
        <x-form.file wire:model="banner" accept="image/*" placeholder="Recommended: 1200 x 675 px (16:9)"></x-form.file>
        <x-tooltip class="!absolute -top-4 right-0" message="Upload a visually appealing image to represent your article. Recommended dimensions: 1200 x 675 pixels (16:9 aspect ratio)."></x-tooltip>
      </div>

      @error('banner')
        <div class="!mt-2 text-red-500">{{ $message }}</div>
      @enderror
    </div>

    {{-- SEO SETTINGS --}}
    <h2 class="!font-bold !text-xl !mb-10">SEO Settings (Optional)</h2>
    <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
      <x-form.input wire:model="fields.seo_title" label="Meta Title" placeholder="Enter meta title (for search engines)." />

      <x-form.textarea-counter 
        wire:model="fields.seo_text"
        label="Meta Description"
        placeholder="Enter meta description (for search engines)." 
      ></x-form.textarea-counter>
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-start items-stretch !gap-4">
      <x-btn class="sm:!w-auto !m-0 sm:!px-12" outlined>Save as Draft</x-btn>
      <x-btn wire:click.prevent="submit" class="sm:!w-auto sm:!px-28" >Publish Now</x-btn>
    </div>
  </div>
</div>

