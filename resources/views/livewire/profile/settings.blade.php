<div>
    <div class="flex justify-start items-start !gap-10">

        <div class="w-full">
            <div class="flex justify-start items-center !mb-10">
                <div class="font-bold text-2xl mr-4">Profile</div>
                <div class="flex justify-start items-center gap-2">
                    <x-btn class="!px-4 !py-2">Save</x-btn>
                    <x-btn class="!px-4 !py-2" outlined>Cancel</x-btn>
                </div>
            </div>

            <div class="flex flex-col justify-start items-start !gap-6 !mb-15">
              <x-form.input 
                label="Full Name" 
                value="{{ $user->options->full_name ?? $user->getName() }}" 
                class=""
              />

              <x-form.input 
                label="Username" 
                value="{{ $user->options->full_name ?? $user->getName() }}" 
                class=""
              />
            </div>

            <div class="flex justify-start items-center !mb-10">
                <div class="font-bold text-2xl mr-4">Security</div>
            </div>


            <div class="flex flex-col justify-start items-start !gap-6 !mb-15">
              <x-form.input 
                label="Email"
                type="email" 
                value="{{ $user->email }}" 
                class=""
              />

              <x-form.input 
                label="Password"
                type="password"
                value="{{ $user->options->full_name ?? $user->getName() }}" 
                class=""
              />

              <x-form.toggle 
                label="Two-Factor Authentication"
              />
            </div>

            <div class="flex justify-between items-center !mb-10">
                <div class="font-bold text-2xl mr-4">Payment Methods</div>
                <x-btn class="!text-sm !px-4 !py-1.5 !w-auto !bg-second !border-second">Add Payment Method</x-btn>
            </div>

            <div class="flex flex-col">
              <div class="w-full bg-light rounded !p-4 relative flex items-center justify-between">
                <label for="" class="relative">
                  <input type="checkbox" class="!w-0 !h-0 !opacity-0">
                  <div class="flex flex-col">
                    <div class="">Visa</div>
                    <div class="text-active">USD</div>
                  </div>
                </label>
                <x-chat.editor wrapClass="!bg-white !p-4 !flex-row rounded !gap-4">
                    <x-link>Edit</x-link>
                    <x-link>Delete</x-link>
                </x-chat.editor>
              </div>
            </div>

        </div>

        <div class="!w-45 !h-45 shrink-0 rounded-full overflow-hidden mr-10">
            <img class="object-cover w-full h-full" src="{{ $user->avatar }}" alt="Avatar">
        </div>
    </div>
</div>
