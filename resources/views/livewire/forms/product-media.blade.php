<div>
  <h1 class="!font-normal !m-0 !mb-10">Create Product - Media & Files</h1>

  @php
    $breadcrumbs = [
      'My Account' => route('profile'),
      'My Products' => route('profile.products'),
      "Create Product (2/2)" => route('profile.products.create.media'),
    ];
  @endphp
  <x-breadcrumbs class="!mb-10" :breadcrumbs="$breadcrumbs" />

  <div class="max-w-4xl">

      {{-- MEDIA --}}
      <h2 class="!font-bold !text-2xl !mb-10">Product Media & Files</h2>
      <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
        
        {{-- BANNER --}}
        <div class="relative">
          
          <div class="max-w-sm !mb-6 relative">
            <div wire:loading class="absolute w-full h-full bg-light/50 z-150">
              <x-loader width="60" height="60" />
            </div>

            @if($this->fields['banner']['uploaded'])
              <img class="w-full" src="{{ $this->fields['banner']['uploaded']->temporaryUrl() }}" alt="Banner">
            @elseif ($this->fields['banner']['preview'])
              <img class="w-full" src="{{ $this->fields['banner']['preview'] ?? '' }}" alt="Banner">
            @endif
          </div>

          <x-form.file wire:model="fields.banner.uploaded" label="Featured Photo" accept="image/*"></x-form.file>

          @error('banner')
            <div class="!mt-2 text-red-500">{{ $message }}</div>
          @enderror
        </div>

        {{-- PHOTOS --}}
        <div class="">
          <div class="text-gray !mb-2">Additional Photos</div>
          <div class="flex justify-start items-start !gap-2 flex-wrap">
            @foreach($this->fields['gallery'] as $key => $item)
              @php
                $has_image = boolval($item['uploaded'] || $item['preview']);
              @endphp

              <div class="relative">

                  <x-form.file 
                    wire:model.defer="fields.gallery.{{ $key }}.uploaded" 
                    :delete="!empty($item['uploaded']) || !empty($item['preview'])"
                    accept="image/*" 
                    wrapClass="relative z-50 transition {{ $has_image ? '!text-white group-hover:!text-active' : '' }}"
                  >
                    <div wire:loading class="absolute w-full h-full top-0 left-0 bg-light/50 z-150">
                      <x-loader width="60" height="60" />
                    </div>
                    @if($item['uploaded'])
                      <div class="absolute w-full h-full top-0 left-0 !rounded-lg overflow-hidden z-40 group-hover:cursor-pointer">
                        <img class="object-cover h-full w-full !inline-block opacity-100 transition group-hover:!opacity-50" src="{{ $item['uploaded']->temporaryUrl() }}" alt="Banner">
                      </div>
                    @elseif ($item['preview'])
                      <div class="absolute w-full h-full top-0 left-0 !rounded-lg overflow-hidden z-40 group-hover:cursor-pointer">
                        <img class="object-cover h-full w-full !inline-block opacity-100 transition group-hover:!opacity-50" src="{{ $item['preview'] ?? '' }}" alt="Banner">
                      </div>
                    @endif

                    <x-slot name="drop">
                      @if($item['uploaded'] || $item['preview'])
                        <div wire:click.prevent="dropPhoto('{{ $key }}')" class="!mt-2 text-center flex justify-center items-center hover:cursor-pointer hover:text-active">
                          @include('icons.close', ['width' => 9, 'height' => 9])
                        </div>
                      @endif
                    </x-slot>
                  </x-form.file>
              </div>

            @endforeach
          </div>
        </div>

      </div>

      <h2 class="!font-bold !text-2xl !mb-10">Product Files</h2>
      <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
        
        {{-- PP TEXT --}}
        <div class="">
          <x-form.text-editor wire:model="fields.pp_text" :image="false" label="Post-Purchase Text (Optional):" placeholder="Start writing your post-purchase text here..."></x-form.text-editor>
        </div>

        {{-- PRODUCT FILES --}}
        <div class="!mb-10">
          <div class="!mb-4 !text-sm sm:!text-base">Upload up to 8 files of any file type for your product. Each file can be up to 100MB in size. You can upload a large video to platforms such as YouTube or Vimeo and embed the link below.</div>
          <div class="flex justify-start items-start !gap-2 flex-wrap">
            @foreach($this->fields['files'] as $key => $file)
              @php
                $filename = empty($file['uploaded']) ? (empty($file['current']) ? null : $file['current']) : $file['uploaded']->getClientOriginalName();
              @endphp
              <div class="flex flex-col justify-center items-center hover:cursor-pointer">
                <x-form.file 
                  wire:model="fields.files.{{ $key }}.uploaded" 
                  type="file" 
                  accept="*/*"
                  :filename="$filename"
                >
                  <div wire:loading class="absolute w-full h-full top-0 left-0 bg-light/50 z-150">
                    <x-loader width="60" height="60" />
                  </div>
                  <x-slot name="drop">
                    @if($filename)
                      <div class="flex justify-center items-center !gap-6">
                        <div wire:click.prevent="$dispatch('openModal', { modalName: 'file-description', args: { filename: '{{ $filename }}', key: '{{ $key }}', description: '{{ $file['description'] ?? '' }}' } })" class="font-light text-xl transition hover:text-active">+</div>

                        <div wire:click.prevent="dropFile('{{ $key }}')" class="text-center flex justify-center items-center hover:cursor-pointer hover:text-active">
                          @include('icons.close', ['width' => 9, 'height' => 9])
                        </div>
                      </div>
                    @endif
                  </x-slot>
                </x-form.file>
              </div>
            @endforeach
          </div>
          <div class="text-sm text-gray !mt-2">You can add a description to each file (optional)</div>
        </div>
        

        {{-- LINKS --}}
        <div class="">
          <h2 class="!font-bold !text-2xl !mb-10 relative !inline-block !pr-6">
            Video Link (Optional)
            <x-tooltip message="Optional links to video content that will be delivered to the customer after purchase. Use this to provide access to video courses, tutorials, or exclusive video content hosted on platforms like YouTube or Vimeo."></x-tooltip>
          </h2>
          <div class="flex flex-col justify-start items-stretch !gap-2">
            @foreach ($this->fields['links'] as $key => $link)
              <div class="flex justify-between items-stretch">
                <x-form.input wire:model="fields.links.{{ $key }}.link" :tooltip="false" placeholder="Link" />
                <span wire:click="addLink" class="text-2xl !font-light !p-3 !leading-6 transition hover:cursor-pointer hover:text-active">+</span>
              </div>
            @endforeach
          </div>
        </div>

        {{-- BUTTONS --}}
        <div class="flex justify-start items-stretch !gap-2 sm:!gap-4 flex-wrap sm:!flex-nowrap">
          <x-btn wire:click.prevent="prevStep" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" gray>Back to Details</x-btn>
          <x-btn wire:click.prevent="draft" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" outlined>Save as Draft</x-btn>
          <x-btn wire:click.prevent="submit" class="!max-w-none sm:!max-w-sm">Save & Continue</x-btn>
        </div>
      </div>
  </div>
</div>
