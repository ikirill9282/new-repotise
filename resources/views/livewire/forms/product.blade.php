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
                  <x-form.input-counter 
                    wire:model="fields.title" 
                    name="title"
                    label="Product Title" 
                    placeholder="Enter your article title here..."
                    :max="70"
                    :tooltip="false"
                  />
                </div>

                <div class="">
                  <x-form.chips 
                    name="types"
                    create="false" 
                    entangle="types" 
                    source="types" 
                    label="Product Type" 
                    placeholder="Select product types... (Up to 5)" 
                    :max="5"
                    tooltipText="Select up to 5 types that define your product's format (e.g., Travel Guide, Map, Preset). This clarifies what customers are buying."
                  />
                </div>

                <div class="">
                  <x-form.select 
                    name="refund_policy"
                    wire:model.live="fields.refund_policy"
                    title="Refund Policy" 
                    :options="[0 => 'No returns', 7 => '7 days', 14 => '14 days', 30 => '30 days', 90 => '90 days']"
                  ></x-form.select>
                </div>

                {{-- DESCRIPTION --}}
                <div class="!font-bold !text-2xl !mt-4">Product Description</div>
                <x-form.text-editor wire:model="fields.text" name="text" placeholder="Start writing your product description here..." :max="10000"></x-form.text-editor>

                <div class="">
                  <x-form.chips name="locations" max="3" entangle="locations" source="locations" label="Location" placeholder="Enter city or country...(Up to 3)" tooltipText="Enter relevant key locations for your product. Use up to 3 locations. Locations will help customers find your product and improve their search experience." maxErrorMessage="You can add a maximum of 3 locations." />
                </div>

                <div class="">
                  <x-form.chips name="categories" entangle="categories" source="categories" label="Categories" placeholder="Search or create сategories...(Up to 5)" tooltipText="Enter relevant keywords to categorize your product. Use up to 5 сategories. Categories help customers find your product and improve searchability" />
                </div>
            </div>
        </div>

        {{-- PRICE --}}
        <h2 class="!font-bold !text-2xl !mb-10">Pricing & SEO</h2>
        
        {{-- SUBSCRIPTION CHECKBOX --}}
        <div class="!mb-10">
          <x-form.checkbox wire:model="fields.subscription" :checked="(boolean)$this->fields['subscription']" label="Subscription" />
        </div>

        {{-- PRICING FIELDS --}}
        <div x-data="{ subscription: @entangle('fields.subscription') }">
          {{-- ONE-TIME PURCHASE: Price and Sale Price --}}
          <div x-show="!subscription" class="flex flex-col xs:flex-row justify-start items-start !gap-4 !mb-10">
            <div class="w-full">
              <x-form.input 
                name="price"
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
                name="sale_price"
                wire:model="fields.old_price"
                :tooltip="false" 
                label="Sale Price"
                placeholder="$10" 
                data-input="price" 
                x-ref="input2"
                x-init="() => $refs.input2.dispatchEvent(new Event('input')) " 
              />
            </div>
          </div>

          {{-- SUBSCRIPTION: Price per Month --}}
          <div x-show="subscription" class="!mb-10">
            <div class="!mb-10">
              <x-form.input 
                name="price"
                wire:model="fields.price"
                :tooltip="false" 
                label="Price per Month" 
                placeholder="$50" 
                data-input="price"
                x-ref="input1" 
                x-init="() => $refs.input1.dispatchEvent(new Event('input')) " 
              />
            </div>

            {{-- PREPAYMENT DISCOUNTS --}}
            <h3 class="!font-bold !text-xl !mb-4">Prepayment Discounts (Optional)</h3>
            <p class="!mb-6 !text-gray">Offer a percentage discount for customers who pay upfront.</p>
            
            <div class="grid grid-cols-2 !gap-4">
              <div class="">
                <x-form.input 
                  x-data="{}"  
                  x-init="() => $refs.input.dispatchEvent(new Event('input')) " 
                  x-ref="input" 
                  wire:model="subprice.quarter" 
                  :tooltip="true"
                  tooltipText="Enter the discount for a 3-month prepayment. This should be a better deal than the monthly price to encourage a longer commitment."
                  label="Quarter:" 
                  placeholder="%" 
                  data-input="percent" 
                />
                @error('quarter')
                  <div class="!mt-2 text-red-500">{{ $message }}</div>
                @enderror
              </div>
              <div class="">
                <x-form.input 
                  x-data="{}"  
                  x-init="() => $refs.input.dispatchEvent(new Event('input')) " 
                  x-ref="input" 
                  wire:model="subprice.year" 
                  :tooltip="true"
                  tooltipText="Your best value offer for a 12-month upfront payment. This should be your highest discount to maximize long-term subscriber value."
                  label="Year:" 
                  placeholder="%" 
                  data-input="percent" 
                />
                @error('year')
                  <div class="!mt-2 text-red-500">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>
        
        {{-- SEO SETTINGS --}}
        <h2 class="!font-bold !text-xl !mb-10">SEO Settings (Optional)</h2>
        <div class="flex flex-col justify-start items-stretch !gap-6 !mb-10">
          <x-form.input-counter 
              name="seo_title" 
              wire:model="fields.seo_title" 
              label="Meta Title" 
              placeholder="Enter meta title (for search engines)."
              :max="70"
              :tooltip="true"
              tooltipText="Your product's headline in search results. Keep it under 60 characters and include main keywords."
          />

          <x-form.textarea-counter 
              name="seo_text"
              wire:model="fields.seo_text"
              label="Meta Description"
              placeholder="Enter meta description (for search engines)."
              :max="160"
              tooltipText="The summary shown under your title in search results. Write compelling ad copy under 160 characters to win the click."
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