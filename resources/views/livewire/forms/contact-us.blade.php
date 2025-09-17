<div>
  <x-card class="lg:!p-12">
    <div class="flex justify-between items-stretch !gap-12 flex-col-reverse lg:flex-row">
      <div class="basis-full lg:basis-1/2">
        <x-title class="!mb-4">Get in Touch</x-title>
        <div class="!mb-8">Have a question? We're here to assist. Send us a message.</div>
        <div class="flex flex-col !gap-3">
          <x-form.input name="name" placeholder="Your Name" />

          <x-form.input type="email" name="email" placeholder="Email" />

          <x-form.select label="Select subject..." :options="['subject' => 'Subject', 'subject2' => 'Subject2', 'subject3' => 'Subject3',]" />

          <x-form.textarea class="min-h-24" id="ta" placeholder="Your Message<br> Please provide details about your request"></x-form.textarea>

          <x-form.file></x-form.file>

          <x-btn class="!w-auto !px-18 self-start">Submit</x-btn>
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
