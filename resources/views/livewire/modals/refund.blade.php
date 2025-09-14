<div>
  {{-- HEADER --}}
  <div class="text-2xl font-semibold pb-6 mb-4 border-b-1 border-gray/30">Request Refund</div>


  {{-- DESCRIPTION --}}
  <div class="mb-4">
    {{-- <div class="text-lg font-semibold mb-3">Description from Creator:</div> --}}
    <div class="">Please tell us why you're requesting a refund for this product. Your feedback helps sellers improve.</div>
  </div>

  {{-- FORM --}}
  <form action="" class="flex flex-col gap-3 mb-4">
      <div class="w-full">
        <x-form.select 
          label="Reason for Refund"
          title="Dropdown Menu"
          labelClass="text-gray"
          class="group"
          :options="[
            'reason1' => 'Reason 1',
            'reason2' => 'Reason 2',
            'reason3' => 'Reason 3',
          ]"
        />
      </div>

      <div class="w-full flex flex-col justify-start items-stretch gap-1 group">
          <label class="text-sm sm:text-base text-gray" for="textarea">Details</label>
          <div
              x-data="{
                message: '',
                max: 100,
                getSymbols() {
                  $refs.ta.style.height = 'auto';
                  $refs.ta.style.height = $refs.ta.scrollHeight + 'px';

                  return this.message.length;
                },
                setMessage(val) {
                  this.message = val;
                }
              }"
              x-init="
                $watch('message', (value) => {
                  if (value.length > max) {
                    setMessage(value.slice(0, max));
                  }
                });
              "
              class="relative w-full grow flex items-center justify-start gap-2 sm:gap-3 bg-light rounded-lg !pl-3 py-3 sm:!ps-3 !pe-20 sm:!pe-2">
              <textarea name="text" rows="1"
                  x-ref="ta"
                  x-model="message"
                  id="textarea"
                  class="chat-textarea transition w-full leading-normal outline-0"
                  placeholder="Please provide more details"></textarea>

              <div class="absolute top-0 right-0 text-gray">
                  <div class="flex justify-center items-center py-3 px-2 sm:!px-3 gap-1 sm:!gap-2">
                      <div class="!text-xs sm:text-base p-1 rounded bg-white">
                          <span x-text="getSymbols"></span>/<span x-text="max"></span>
                      </div>
                      <button class="p-1 !bg-white rounded transition hover:text-black">
                          @include('icons.arrow_right')
                      </button>
                  </div>
              </div>
          </div>
      </div>
  </form>


  {{-- LIST --}}
  <div class="pb-6 mb-4 border-b-1 border-gray/30">
    <ul class="!pl-5 !mb-4">
      <li class="!list-disc mb-2">The seller will review your request. You'll be notified of their decision.</li>
      <li class="!list-disc mb-2">If the seller doesn't process your request within 30 days, the refund may be automatically issued, subject to the conditions below.</li>
      <li class="!list-disc mb-2">Please note: Platform and original payment processing fees associated with this purchase are non-refundable.</li>
      <li class="!list-disc">Important: Refunds are issued directly by the seller from their available account balance. TrekGuider acts as a platform facilitator and is not liable for issuing refunds if the seller's funds are insufficient.</li>
    </ul>
  </div>


  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3 max-w-xl mx-auto">
    <x-btn class="!text-sm sm:!text-base w-auto m-0" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn class="!text-sm sm:!text-base w-auto m-0 grow" >Submit Refund Request</x-btn>
  </div>
</div>
