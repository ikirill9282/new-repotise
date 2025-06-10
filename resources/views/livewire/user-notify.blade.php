<div wire:poll.10s="resetNotifications" class="flex flex-col justify-start gap-2 items-stretch w-full">
  @if($this->notifications->isNotEmpty())
    @foreach ($this->notifications as $notify)
      <div class="w-full p-3 rounded-lg border flex justify-between items-center
                @if($notify->type == 'info')
                  !border-sky-600 !text-sky-600 bg-sky-400/25
                @elseif($notify->type == 'success')
                  !border-emerald-600 !text-emerald-600 bg-emerald-400/25
                @else
                  !border-slate-600 !text-slate-600 bg-slate-400/25
                @endif
              ">
              <span>{!! $notify->message !!}</span>
              @if($notify->closable)
                <span wire:click="markAsRead({{ $notify }})" class="hover:cursor-pointer">@include('icons.close')</span>
              @endif
      </div>
    @endforeach
  @endif
</div>