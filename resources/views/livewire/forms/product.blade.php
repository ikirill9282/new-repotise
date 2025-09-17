<div>
    <h1 class="!font-normal !m-0 !mb-10">
      @if($step == 1)
        Create Product - Details
      @else
        Create Product - Media & Files
      @endif
    </h1>
    @php
      $breadcrumbs = [
        'My Account' => route('profile'),
        'My Products' => route('profile.products'),
      ];
      $breadcrumbs["Create Product ($step/2)"] = route('profile.products.create');
    @endphp
    <x-breadcrumbs class="!mb-10" :breadcrumbs="$breadcrumbs" />
    
    {{-- CONTENT --}}
    <div class="max-w-4xl">
      @if($step == 1)
        <h2 class="!font-bold !text-2xl !mb-10">Product Details</h2>
        <div class="flex flex-col justify-start items-stretch !mb-10">
            <div class="flex flex-col justify-start items-stretch !gap-6">

                <div class="">
                  <x-form.input label="Product Title" placeholder="Enter your article title here..." />
                </div>

                <div class="">
                  <x-form.chips source="types" label="Product Type" placeholder="Select product types... (Up to 5)" />
                </div>

                <div class="">
                  <x-form.checkbox label="Subscription" />
                </div>

                {{-- SUBSCRIPTION --}}
                <div class="">
                  <div class="text-lg inline-block relative !pr-6 !mb-6">
                    Prepayment Discounts:
                    <x-tooltip message="tooltip"></x-tooltip>
                  </div>
                  <div class="grid grid-cols-3 !gap-3">
                    <div class="">
                      <x-form.input :tooltip="false" label="Month:" placeholder="%" />
                    </div>
                    <div class="">
                      <x-form.input :tooltip="false" label="Quarter:" placeholder="%" />
                    </div>
                    <div class="">
                      <x-form.input :tooltip="false" label="Year:" placeholder="%" />
                    </div>
                  </div>
                </div>


                {{-- DESCRIPTION --}}
                <x-form.text-editor label="Product Description" placeholder="Start writing your product description here..."></x-form.text-editor>

                <div class="text-lg">Product Description</div>

                <div class="">
                  <x-form.chips max="3" source="locations" label="Location" placeholder="Enter city or country...(Up to 3)" />
                </div>

                <div class="">
                  <x-form.chips source="categories" label="Categories" placeholder="Search or create сategories...(Up to 5)" />
                </div>
            </div>
        </div>

        {{-- SEO SETTINGS --}}
        <h2 class="!font-bold !text-2xl !mb-10">SEO Settings (Optional)</h2>
        <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
            <x-form.input label="Meta Title" placeholder="Enter meta title (for search engines)." />

            <x-form.textarea :tooltip="true" label="Meta Description"
                placeholder="Enter meta description (for search engines)." class="min-h-18 sm:min-h-25" />
        </div>

      @else

        {{-- MEDIA --}}
        <h2 class="!font-bold !text-2xl !mb-10">Product Media & Files</h2>
        <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
          
          {{-- BANNER --}}
          <div class="">
            <x-form.file label="Featured Photo"></x-form.file>
          </div>

          {{-- PHOTOS --}}
          <div class="">
            <div class="text-gray !mb-2">Additional Photos</div>
            <div class="flex justify-start items-center !gap-2 flex-wrap">
              @for($i = 0; $i < 8; $i++)
                <x-form.file></x-form.file>
              @endfor
            </div>
          </div>

        </div>

        <h2 class="!font-bold !text-2xl !mb-10">Product Files</h2>
        <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
          
          {{-- PP TEXT --}}
          <div class="">
            <x-form.text-editor label="Post-Purchase Text (Optional):" placeholder="Start writing your post-purchase text here..."></x-form.text-editor>
          </div>

          {{-- PRODUCT FILES --}}
          <div class="!mb-10">
            <div class="!mb-4 !text-sm sm:!text-base">Upload up to 8 files of any file type for your product. Each file can be up to 100MB in size. You can upload a large video to platforms such as YouTube or Vimeo and embed the link below.</div>
            <div class="flex justify-start items-center !gap-2 flex-wrap">
              @for($i = 0; $i < 8; $i++)
                <div class="flex flex-col justify-center items-center hover:cursor-pointer">
                  <x-form.file type="file"></x-form.file>
                  <div class="font-light text-xl transition hover:text-active">+</div>
                </div>
              @endfor
            </div>
            <div class="text-sm text-gray !mt-2">You can add a description to each file (optional)</div>
          </div>
          

          {{-- LINKS --}}
          <div class="">
            <h2 class="!font-bold !text-2xl !mb-10 relative !inline-block !pr-6">
              Video Link (Optional)
              <x-tooltip message="tooltip"></x-tooltip>
            </h2>
            <div class="flex flex-col justify-start items-stretch !gap-2">
              <div class="flex justify-between items-stretch">
                <x-form.input :tooltip="false" placeholder="Link" />
                <span class="text-2xl !font-light !p-3 !leading-6 transition hover:cursor-pointer hover:text-active">+</span>
              </div>
              <div class="flex justify-between items-stretch">
                <x-form.input :tooltip="false" placeholder="Link" />
                <span class="text-2xl !font-light !p-3 !leading-6 transition hover:cursor-pointer hover:text-active">+</span>
              </div>
              <div class="flex justify-between items-stretch">
                <x-form.input :tooltip="false" placeholder="Link" />
                <span class="text-2xl !font-light !p-3 !leading-6 transition hover:cursor-pointer hover:text-active">+</span>
              </div>

            </div>
          </div>
        </div>

      @endif
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-start items-stretch !gap-2 sm:!gap-4 flex-wrap sm:!flex-nowrap">
      @if($step == 2)
        <x-btn wire:click.prevent="prevStep" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" gray>Back to Details</x-btn>
      @endif
      <x-btn class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" outlined>Save as Draft</x-btn>
      <x-btn wire:click.prevent="nextStep" class="!max-w-none sm:!max-w-sm">Save & Continue</x-btn>
    </div>
</div>

@script
  <script>
    Livewire.on('stepChanged', function() {
      $(window).scrollTop(0);
    })
  </script>
@endscript