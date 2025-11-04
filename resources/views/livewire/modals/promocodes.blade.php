<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Promo Codes</div>

  {{-- FORM --}}
  <div class="grid grid-cols-1 md:grid-cols-12 items-center !gap-4 xl:!gap-6 !mb-4 xl:!mb-6">
    <div class="md:col-span-6">
      <x-form.input label="Promo Code" placeholder="Enter promo code or generate one" />
    </div>
    <div class="md:col-span-6">
      <x-form.input label="Discount Amount ($)" placeholder="Enter fixed dollar discount" />
    </div>
    <div class="md:col-span-8">
      <x-form.input label="Discount Percentage (%)" placeholder="Enter percentage discount" />
    </div>
    <div class="md:col-span-4 text-sm self-end">
      <div class="lg:!mb-2">Choose products to which this promo code will apply. Leave unchecked to apply to all products.</div>
    </div>
    <div class="md:col-span-6">
      <x-form.select title="Choose products" placeholder="Select Products"></x-form.select>
    </div>
    <div class="md:col-span-6">
      <x-form.input label="Activation Limit" placeholder="Enter maximum activations (leave blank for unlimited)" />
    </div>
    <div class="md:col-span-4 xl:col-span-2">
      <div class="mt-4">
        <x-form.checkbox label="Generate multiple codes" class="text-nowrap" />
      </div>
    </div>
    <div class="md:col-span-4 xl:col-span-5">
      <x-form.input label="Number of Codes to Generate" placeholder="Enter number of codes" />
    </div>
    <div class="md:col-span-4 xl:col-span-5">
      <x-form.input label="Expiration Date" placeholder="Select expiration date (optional)" />
    </div>
  </div>

  {{-- BUTTONS --}}
  <div class="grid grid-cols-12 !gap-4 xl:!gap-6 !mb-6 xl:!mb-6">
    <div class="col-span-6 md:col-span-2">
      <x-btn wire:click.prevent="$dispatch('closeModal')" gray>Cancel</x-btn>
    </div>
    <div class="col-span-6 md:col-span-4">
      <x-btn outlined>Save</x-btn>
    </div>
    <div class="col-span-12 md:col-span-6" title="Unlock professional tools like Promo Codes, Email Marketing, and Unlimited Storage. Click to learn more">
      <x-btn class="!w-auto !max-w-none" inert >Generate 👑</x-btn>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="h-86 !overflow-auto scrollbar-custom scrollbar-custom-gray">
    <table class="table text-sm sm:text-[15px]">
        <thead>
          <tr class="">
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Code</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Products</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Activations</th>
            <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Expiration Date</th>
          </tr>
        </thead>
        <tbody>
          @for($i = 0; $i < 10; $i++)
            <tr>
              <td class="!border-b-gray/15 !py-4 ">
                <div class="flex items-center justify-start !gap-2 group copyToClipboard hover:cursor-pointer" data-target="code_{{ $i }}">
                  <div class="uppercase font-bold transition group-hover:!text-active" data-copyId="code_{{ $i }}">NEWYEAR26</div>
                  <div class="text-nowrap">Discount 15%</div>
                  <div class=""><img src="{{ asset('assets/img/copy-icon.svg') }}" alt="Copy"></div>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 ">
                <div class="read-more read-more-70 text-nowrap" data-text="More">
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                  <p>A Guide to Getting to Know</p>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap">
                <div class="flex items-center justify-start !gap-2">
                  <span>20</span>
                  <span>/</span>
                  <span>
                    @if($i > 3)
                      100
                    @else
                      <img src="{{ asset('assets/img/infinite.svg') }}" alt="Infinite">
                    @endif
                  </span>
                </div>
              </td>
              <td class="!border-b-gray/15 !py-4 text-nowrap !text-gray">05.28.2026</td>
            </tr>
          @endfor
        </tbody>
        <tfoot></tfoot>
    </table>
  </div>
</div>
