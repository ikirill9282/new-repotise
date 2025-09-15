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
          <x-form.input placeholder="Company name" />
        </div>
        <div class="">
          <x-form.select label="Select a topic" />
        </div>
        <div class="">
          <x-form.textarea 
            placeholder="Text your message"
            :tooltip="true"
            class="min-h-24"
            x-on:input="(evt) => {
              const len = evt.target.value.length;
              if (len <= max) setLen(len);
            }"
            x-ref="ta"
          ></x-form.textarea>
          <div class="text-sm !text-gray text-right mt-2">
            <span x-html="len"></span>
            <span>/</span>
            <span x-html="max"></span>
          </div>
        </div>
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
