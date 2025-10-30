<div class="w-full">
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Request Refund</div>

  @if(!empty($errorMessage))
    <div class="p-4 bg-red-50 text-red-500 rounded-lg">{{ $errorMessage }}</div>
  @elseif(!$orderProduct)
    <div class="p-4 bg-light rounded-lg text-gray">
      We couldn’t find this purchase. Please refresh the page and try again.
    </div>
  @else

    <div class="mb-4">
      <div class="">Please tell us why you're requesting a refund for this product. Your feedback helps sellers improve.</div>
    </div>

    <form wire:submit.prevent="submit" class="flex flex-col gap-4 mb-6">
        <div class="w-full">
          <x-form.select 
            name="reason"
            label="Reason for Refund"
            title="Select reason"
            labelClass="text-gray"
            class="group"
            :options="$reasonOptions"
            wire:model="form.reason"
          />
          @error('form.reason')
            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
          @enderror
        </div>

        <div class="w-full flex flex-col justify-start items-stretch gap-1 group">
            <label class="text-sm sm:text-base text-gray" for="refund-details">Details <span class="text-xs text-gray/70">(optional)</span></label>
            <div
                x-data="{
                  max: 1000,
                  updateHeight() {
                    $refs.ta.style.height = 'auto';
                    $refs.ta.style.height = $refs.ta.scrollHeight + 'px';
                  }
                }"
                x-init="updateHeight();"
                class="relative w-full grow flex items-center justify-start gap-2 sm:gap-3 bg-light rounded-lg !pl-3 py-3 sm:!ps-3 !pe-20 sm:!pe-2">
                <textarea
                  x-ref="ta"
                  x-on:input="updateHeight()"
                  id="refund-details"
                  wire:model.defer="form.details"
                  rows="1"
                  maxlength="1000"
                  class="chat-textarea transition w-full leading-normal outline-0 bg-transparent"
                  placeholder="Provide any additional details that might help the seller resolve your issue."
                ></textarea>

                <div class="absolute top-0 right-0 text-gray">
                    <div class="flex justify-center items-center py-3 px-2 sm:!px-3 gap-1 sm:!gap-2">
                        <div class="!text-xs sm:text-sm p-1 rounded bg-white">
                            <span>{{ strlen($form['details'] ?? '') }}</span>/1000
                        </div>
                    </div>
                </div>
            </div>
            @error('form.details')
              <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="pb-6 mb-4 border-b-1 border-gray/30">
          <ul class="!pl-5 !mb-4 text-sm text-gray">
            <li class="!list-disc mb-2">The seller will review your request. You'll be notified of their decision.</li>
            <li class="!list-disc mb-2">If the seller doesn't process your request within 30 days, the refund may be automatically issued, subject to the conditions below.</li>
            <li class="!list-disc mb-2">Platform and payment processing fees are non-refundable.</li>
            <li class="!list-disc">Refunds are issued from the seller's balance. TrekGuider is not liable if the seller lacks sufficient funds.</li>
          </ul>
        </div>

        <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
          <x-btn class="!text-sm sm:!text-base w-auto m-0" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
          <button
            type="submit"
            class="main-btn !p-2.5 !rounded w-auto m-0 grow !text-sm sm:!text-base"
            wire:loading.attr="disabled"
          >
            <span wire:loading.remove>Submit Refund Request</span>
            <span wire:loading>Submitting...</span>
          </button>
        </div>
    </form>
  @endif
</div>
