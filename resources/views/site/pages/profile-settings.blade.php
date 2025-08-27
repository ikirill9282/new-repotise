@extends('layouts.site')

@section('content')
  <x-profile.wrap>
    <x-card>
      <div class="flex justify-start items-start !gap-4">

        <div class="w-full">
          <div class="flex justify-start items-center !mb-10">
            <div class="font-bold text-2xl mr-4">Profile</div>
            <div class="flex justify-start items-center gap-2">
              <x-btn class="!px-4 !py-2" >Save</x-btn>
              <x-btn class="!px-4 !py-2" outlined>Cancel</x-btn>
            </div>
          </div>

          <x-form.input></x-form.input>
        </div>


        <div class="!w-45 !h-45 shrink-0 rounded-full overflow-hidden mr-10">
          <img class="object-cover w-full h-full" src="{{ $user->avatar }}" alt="Avatar">
        </div>
      </div>
    </x-card>
  </x-profile.wrap>
@endsection