<div>
    <h1 class="!font-normal !m-0 !mb-10">Create Product - Details</h1>

    @php
      $breadcrumbs = [
        'My Account' => route('profile'),
        'My Products' => route('profile.products'),
        "Create Product (1/2)" => route('profile.products.create'),
      ];
    @endphp
    
    <x-breadcrumbs class="!mb-10" :breadcrumbs="$breadcrumbs"></x-breadcrumbs>


    {{-- CONTENT --}}
    <div class="max-w-4xl">
        <h2 class="!font-bold !text-2xl !mb-10">Product Details</h2>
        <div class="flex flex-col justify-start items-stretch !mb-10">
            <div class="flex flex-col justify-start items-stretch !gap-6">

                <div class="">
                  <x-form.input 
                    wire:model="fields.title" 
                    name="title"
                    label="Product Title" 
                    placeholder="Enter your article title here..." 
                  />
                </div>

                <div class="">
                  <x-form.chips 
                    name="loactions"
                    create="false" 
                    entangle="types" 
                    source="types" 
                    label="Product Type" 
                    placeholder="Select product types... (Up to 5)" 
                  />
                </div>

                <div class="">
                  <x-form.select 
                    wire:model.live="fields.refund_policy"
                    title="Refund Policy" 
                    :options="[30 => '30 days', 60 => '60 days', 90 => '90 days',]"
                  ></x-form.select>
                </div>


                <div class="">
                  <x-form.checkbox wire:model="fields.subscription" :checked="(boolean)$this->fields['subscription']" label="Subscription" />
                </div>

                {{-- SUBSCRIPTION --}}
                <div x-data="{ show: @entangle('fields.subscription') }" x-show="show" class="">
                  <div class="!font-semibold !text-2xl inline-block relative !pr-6 !mb-10">
                    Prepayment Discounts:
                    <x-tooltip message="Specify the discounts you want to give to users who subscribe for a longer period of time."></x-tooltip>
                  </div>
                  <div class="grid grid-cols-3 !gap-3">
                    <div class="">
                      <x-form.input x-data="{}"  x-init="() => $refs.input.dispatchEvent(new Event('input')) " x-ref="input" wire:model="subprice.month" :tooltip="false" label="Month:" placeholder="%" data-input="percent" />
                      @error('month')
                        <div class="!mt-2 text-red-500">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="">
                      <x-form.input x-data="{}"  x-init="() => $refs.input.dispatchEvent(new Event('input')) " x-ref="input" wire:model="subprice.quarter" :tooltip="false" label="Quarter:" placeholder="%" data-input="percent" />
                      @error('quarter')
                        <div class="!mt-2 text-red-500">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="">
                      <x-form.input x-data="{}"  x-init="() => $refs.input.dispatchEvent(new Event('input')) " x-ref="input" wire:model="subprice.year" :tooltip="false" label="Year:" placeholder="%" data-input="percent" />
                      @error('year')
                        <div class="!mt-2 text-red-500">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>


                {{-- DESCRIPTION --}}
                <x-form.text-editor wire:model="fields.text" name="text" label="Product Description" placeholder="Start writing your product description here..."></x-form.text-editor>

                <div class="!font-semibold !text-2xl !py-4">Product Description</div>

                <div class="">
                  <x-form.chips max="3" entangle="locations" source="locations" label="Location" placeholder="Enter city or country...(Up to 3)" tooltipText="Enter relevant key locations for your product. Use up to 3 locations. Locations will help customers find your product and improve their search experience." />
                </div>

                <div class="">
                  <x-form.chips entangle="categories" source="categories" label="Categories" placeholder="Search or create сategories...(Up to 5)" tooltipText="Enter relevant keywords to categorize your product. Use up to 5 categories. Categories help customers find your product and improve searchability." />
                </div>
            </div>
        </div>

        {{-- PRICE --}}
        <h2 class="!font-bold !text-2xl !mb-10">Pricing & SEO</h2>
        <div x-data="{}" class="flex flex-col xs:flex-row justify-start items-center !gap-4 !mb-10">
          <div class="w-full">
            <x-form.input 
              wire:model="fields.price"
              :tooltip="false" 
              label="Price" 
              placeholder="$10" 
              data-input="price"
              x-ref="input1" 
              x-init="() => $refs.input1.dispatchEvent(new Event('input')) " 
            />
          </div>
          <div class="w-full">
            <x-form.input 
              wire:model="fields.old_price"
              :tooltip="false" 
              label="Sale Price"
              placeholder="$10" 
              data-input="price" 
              x-ref="input2"
              x-init="() => $refs.input2.dispatchEvent(new Event('input')) " 
            />
          </div>
          @error('price')
            <div class="!mt-2 text-red-500">{{ $message }}</div>
          @enderror
        </div>
        
        {{-- SEO SETTINGS --}}
        <h2 class="!font-bold !text-2xl !mb-10">SEO Settings (Optional)</h2>
        <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
          <x-form.input wire:model="fields.seo_title" label="Meta Title" placeholder="Enter meta title (for search engines)." />

          <x-form.textarea-counter 
              wire:model="fields.seo_text"
              label="Meta Description"
              placeholder="Enter meta description (for search engines)." 
            ></x-form.textarea-counter>
        </div>

      
    </div>

    {{-- BUTTONS --}}
    <div class="flex justify-start items-stretch !gap-2 sm:!gap-4 flex-wrap sm:!flex-nowrap">
      @if($step == 2)
        <x-btn wire:click.prevent="prevStep" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" gray>Back to Details</x-btn>
      @endif
      <x-btn wire:click.prevent="draft" class="shrink-0 sm:!w-auto !m-0 sm:!px-10 md:!px-12 !max-w-[calc(50%_-_0.25rem)] sm:max-w-none" outlined>Save as Draft</x-btn>
      <x-btn wire:click.prevent="submit" class="!max-w-none sm:!max-w-sm">Save & Continue</x-btn>
    </div>
</div>

@script
  <script>
    Livewire.on('stepChanged', function() {
      $(window).scrollTop(0);
    })
  </script>
@endscript