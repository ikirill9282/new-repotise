@props([
    'child' => false,
    'message' => null,
    'author_id' => null,
    'resource' => null,
    'level' => 1,
])


<div class="message text-sm sm:text-base py-3 flex justify-start items-stretch gap-1 sm:gap-3 w-full overflow-x-scroll {{ $child ? '' : 'border-b border-gray/50 border-collapse' }}">
    <div class="avatar flex flex-col justify-center items-center gap-2 select-none">
        <div class="w-9 h-9 sm:w-15 sm:h-15 rounded-full overflow-hidden">
            <a href="{{ $message->author->makeProfileUrl() }}">
                <img src="{{ $message->author->avatar }}" alt="" class="w-full h-full object-cover">
            </a>
        </div>
        <div
            class="grow w-full relative {{ $child ? 'after:content-none' : 'after:content-[\'\']' }} after:absolute after:top-0 after:left-[50%] after:translate-x-[-50%] after:h-full after:w-[1px] after:bg-gray/50 after:rounded">
        </div>
    </div>

    <div class="message-wrap flex flex-wrap grow">
        <div class="content flex flex-col grow gap-1.5 relative pr-12">
            <div class="username">
                <a class="text-black hover:!text-active transition" href="{{ $message->author->makeProfileUrl() }}">
                    {{ $message->author->profile }}
                </a>
                {{-- #{{ $message->id }} --}}
            </div>
            <div class="created-at text-sm text-gray">
                {{ \Illuminate\Support\Carbon::parse($message->created_at)->format('d.m.Y') }}</div>
            <div class="message-text read-more">{{ $message->text }}</div>
            
            @if ($message->edited)
              <div class="text-sm text-gray">Edited</div>
            @endif

            <div class="likes flex justify-start items-center gap-2">
                @if($resource == 'review' && $level == 1)
                  @if((auth()->user() && auth()->user()->id == $author_id) || auth()->user()->hasRole(['admin', 'super-admin']))
                    <div class="reply reply-button" data-reply="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $message->id]) }}">
                        <a href=""
                            class="!text-gray border-b border-dashed pb-0.5 transition hover:!text-active"
                          >
                            Reply
                          </a>
                    </div>
                  @endif
                @elseif($resource == 'review' && $level == 2 && auth()->user()->hasRole(['admin', 'super-admin']))
                  <div class="reply reply-button" data-reply="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $message->id]) }}">
                        <a href=""
                            class="!text-gray border-b border-dashed pb-0.5 transition hover:!text-active"
                          >
                            Reply
                          </a>
                    </div>
                @endif
                
                @if($resource == 'comment')
                    @if(auth()->user())
                    <div class="reply reply-button" data-reply="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $message->id]) }}">
                        <a href=""
                            class="!text-gray border-b border-dashed pb-0.5 transition hover:!text-active"
                          >
                            Reply
                          </a>
                    </div>
                  @endif
                @endif

                <div class="flex">
                    @foreach ($message->likes as $k => $like)
                        <div class="rounded-full overflow-hidden w-4 h-4 sm:w-6 sm:h-6 {{ $k > 0 ? 'ml-[-5px]' : '' }}">
                            <a href="{{ $like->author->makeProfileUrl() }}" class="">
                                <img class="object-cover w-full h-full" src="{{ $like->author->avatar }}"
                                    alt="{{ $like->author->getName() }}">
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-start gap-1">
                    @php
                      $like_id = \App\Helpers\CustomEncrypt::generateUrlHash([$message->id]);
                    @endphp
                    <a 
                      href="/feedback/likes"
                      class="text-transparent hover:cursor-pointer feedback_button group 
                             {{ auth()->check() ? '' : 'open_auth' }}
                             {{ is_liked('review', $message->id) ? 'liked' : '' }}
                            " 
                      data-item="{{ hash_like('review', $message->id) }}"
                      data-id="{{ $like_id }}"
                      >
                        @include('icons.thumb', ['width' => 20, 'height' => 20])
                    </a>
                    <div class="" data-counter="{{ $like_id }}">{{ $message->likes_count }}</div>
                </div>
            </div>

            <div class="settings flex flex-col absolute top-0 right-0 h-full">
                <x-chat.editor 
                  target="{{ \App\Helpers\CustomEncrypt::generateUrlHash(['id' => $message->id]) }}"
                  :message_author_id="$message->author->id"
                >
                </x-chat.editor>
                <div class="flex items-center justify-start mt-auto ml-[-0.25rem] select-none">
                    <div class="text-yellow">
                        @include('icons.star', ['width' => 20, 'height' => 20])
                    </div>
                    {{ $message->rating }}
                </div>
            </div>
        </div>
        
        @php
          $arr = $message->toArray();
        @endphp

        @if (array_key_exists('messages', $arr) && !empty($arr['messages']))
          @foreach ($message->messages as $child_message)
            <x-chat.message 
              :message="$child_message" 
              :child="true" 
              :resource="$resource"
              :author_id="$author_id"
              :level="($level + 1)"
            >
          </x-chat.message>
          @endforeach
        @endif

        @if($message->getUnloadedMessagesCount() > 0)
          <x-chat.more
            :resource="\App\Helpers\CustomEncrypt::generateUrlHash([
              'id' => $message->id,
              'offset' => $message->getLoadedMessagesCount(),
              'type' => 'child',
              'resource' => $resource,
              'level' => $level + 1,
            ])"
            class="!flex w-full"
          >
            Show More Replies ({{ $message->getLoadingMessagesCount() }} of {{ $message->getUnloadedMessagesCount() }})
          </x-chat.more>
        @endif
    </div>
</div>
