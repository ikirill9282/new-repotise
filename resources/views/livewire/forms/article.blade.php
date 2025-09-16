<div>
  <h2 class="!font-bold !text-xl !mb-10">Create New Article</h2>
  <div class="flex flex-col justify-start items-stretch max-w-4xl">
    <div class="flex flex-col justify-start items-stretch !gap-6">
      
      <x-form.input label="Article Title" placeholder="Enter your article title here..." />

      <div class="relative">
        <div class="!mb-2 text-gray">Article Content</div>
        <div 
          class="quill-editor !bg-light !border-none !rounded-lg min-h-36 !pr-4 !text-base" 
          data-placeholder="Start writing your article here..."
        ></div>
        <x-tooltip class="!right-3 !top-25" message="tooltip"></x-tooltip>
      </div>

      <div class="">
        <x-form.chips name="tags" label="Tags" placeholder="Search or create tags...(Up to 5)" />
      </div>
    </div>
  </div>
</div>
