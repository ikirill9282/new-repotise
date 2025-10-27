<div>
  <x-card class="lg:!p-12">
    <div class="flex justify-between items-stretch !gap-12 flex-col-reverse lg:flex-row">
      <div class="basis-full lg:basis-1/2">
        <x-title class="!mb-4">Get in Touch</x-title>
        
        <div class="!mb-8">Have a question? We're here to assist. Send us a message.</div>
        
        <div class="flex flex-col !gap-3">

          @csrf

          <x-form.input wire:model="fields.name" :tooltip="false" name="name" placeholder="Your Name"  />

          <x-form.input wire:model="fields.email" type="email" name="email" placeholder="Email" :tooltip="false" />

          <x-form.select wire:model="fields.subject" name="subject" label="Select subject..." :options="['Order Inquiry' => 'Order Inquiry', 'Account Inquiry' => 'Account Inquiry', 'Technical Support' => 'Technical Support','Verification Request' => 'Verification Request','Creator Inquiry' => 'Creator Inquiry','Business Partnership' => 'Business Partnership','Suggestions & Feedback' => 'Suggestions & Feedback','Report an Issue' => 'Report an Issue','Legal Inquiry' => 'Legal Inquiry','General Inquiry' => 'General Inquiry','subject3' => 'Subject3','subject3' => 'Subject3','subject3' => 'Subject3','subject3' => 'Subject3','subject3' => 'Subject3',]" :tooltip="false" />
          {{-- <x-form.select wire:model="fields.subject" name="subject" label="Select subject..." :options="['subject' => 'Subject', 'subject2' => 'Subject2', 'subject3' => 'Subject3',]" :tooltip="false" /> --}}


          <x-form.textarea-counter wire:model="fields.text" name="text" class="min-h-24" id="ta" placeholder="Your Message<br> Please provide details about your request" :tooltip="false"></x-form.textarea-counter>

          <x-form.file id="contact-file" wire:model="fields.file" accept="image/*">
            <div wire:loading class="absolute w-full h-full top-0 left-0 bg-light/50 z-150">
              <x-loader width="60" height="60" />
            </div>

            @if($this->fields['file'])
              <div class="absolute w-full h-full top-0 left-0 !rounded-lg overflow-hidden z-40 group-hover:cursor-pointer">
                <img class="object-cover h-full w-full !inline-block opacity-100 transition group-hover:!opacity-50" src="{{ $this->fields['file']->temporaryUrl() }}" alt="Banner">
              </div>
            @endif
          </x-form.file>
					<script>
						document.addEventListener('DOMContentLoaded', function () {
								const input = document.getElementById('contact-file');
								if (input) {
										input.addEventListener('change', function(event) {
												const file = event.target.files[0];
												if (file && file.size > 25 * 1024 * 1024) {
														alert('Файл слишком большой. Максимальный размер — 25 МБ.');
														event.target.value = ''; // Очистить input
												}
										});
								}
						});
						</script>

          <x-btn wire:click.prevent="submit" class="!w-auto !px-18 self-start">Submit</x-btn>

        </div>
      </div>

      <div class="basis-full lg:basis-1/2">
        <div class="!leading-0">
          <img class="mx-auto" src="{{ asset('/assets/img/img_touch.png') }}" alt="Form Image">
        </div>
      </div>
    </div>
  </x-card>
</div>
