<div>
  <div class="max-w-4xl">
    @dump($this->fields)
    {{-- CONTENT --}}
    <h2 class="!font-bold !text-xl !mb-10">Create New Article</h2>
    <div class="flex flex-col justify-start items-stretch !mb-10">
      <div class="flex flex-col justify-start items-stretch !gap-6">
        
        <x-form.input wire:model="fields.title" label="Article Title" placeholder="Enter your article title here..." />
    
        <x-form.text-editor wire:model="fields.text" label="Article Content" placeholder="Start writing your article here..."></x-form.text-editor>

        <div class="">
          <x-form.chips source="tags" name="tags" label="Tags" placeholder="Search or create tags...(Up to 5)" />
        </div>
        
        <div class="">
          <x-form.datepicker label="Publish Date" placeholder="Schedule a publication date MM/DD/YEAR" />
        </div>
      </div>
    </div>

    {{-- BANNER --}}
    <h2 class="!font-bold !text-xl !mb-10">Featured Image</h2>
    <div class="flex flex-col justify-start items-stretch !mb-10">
      <x-form.file placeholder="350x100 px"></x-form.file>
    </div>

    {{-- SEO SETTINGS --}}
    <h2 class="!font-bold !text-xl !mb-10">SEO Settings (Optional)</h2>
    <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
      <x-form.input label="Meta Title" placeholder="Enter meta title (for search engines)." />

      <x-form.textarea 
        :tooltip="true" 
        label="Meta Description" 
        placeholder="Enter meta description (for search engines)." 
        class="min-h-18 sm:min-h-25"
      />
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-start items-stretch !gap-4">
      <x-btn class="sm:!w-auto !m-0 sm:!px-12" outlined>Save as Draft</x-btn>
      <x-btn wire:click.prevent="submit" class="sm:!w-auto sm:!px-28" >Publish Now</x-btn>
    </div>
  </div>
</div>
