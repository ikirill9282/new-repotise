<div>

  <div class="flex justify-betweeen items-stretch !gap-12 flex-col lg:flex-row">
    <div class="basis-1/2">
      {{-- HEAD --}}
      <div class="!text-2xl !font-semibold !mb-4">Let's Connect</div>
      <div class="max-w-md !mb-10">
        Ready to explore how partnering with TrekGuider can benefit your business? Reach out to our partnership team today!
      </div>


      {{-- FORM --}}
      <div
        x-data="{
          len: 0,
          max: 500,
          setLen(val) {
            this.len = val;
          }
        }"
        class="flex flex-col justify-between items-stretch !gap-3">
        <div class="">
          <x-form.input wire:model="fields.name" name="name" placeholder="Company name" />
        </div>

        <div class="">
          <x-form.select 
            wire:model="fields.topic"
            label="Select a topic" 
            name="topic"
            :options="[
              'topic1' => 'topic1', 
              'topic2' => 'topic2', 
              'topic3' => 'topic3',
            ]" 
          />
        </div>

        <x-form.textarea-counter wire:model="fields.text" name="text" placeholder="Text your message"></x-form.textarea-counter>

        <x-btn wire:click.prevent="submit" class="sm:!w-auto self-center lg:self-start sm:!px-12">Start Partnership</x-btn>
      </div>
    </div>


    <div class="basis-1/2">
      {{-- LOGO --}}
      <div class="w-full">
        <img class="mx-auto" src="{{ asset('assets/img/formImage.png') }}" alt="Investment" />
      </div>
    </div>
  </div>
</div>
