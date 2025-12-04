@props([
  'id' => null,
  'user_id' => null,
  'variables' => collect([]),
])
@php
  $hash_id = \App\Helpers\CustomEncrypt::generateUrlHash([$id]);
@endphp
<div class="right_edit h-0 transition overflow-hidden" id="editor-{{ $id }}"
  data-comment="{{ $id }}">
  <a href="#" 
     x-on:click.prevent="Livewire.dispatch('openModal', { modalName: 'report', args: { model: '{{ $hash_id }}', resource: 'comment' } })">
    {{ print_var('comment_report_message', $variables) }}
  </a>

  @if (auth()->check() && (auth()->user()->id == $user_id || auth()->user()->hasRole('admin')))
      <a href="#">{{ print_var('comment_edit_message', $variables) }}</a>
  @endif

  @if (auth()->check() && auth()->user()->hasRole('admin'))
      <a href="#">{{ print_var('comment_delete_message', $variables) }}</a>
  @endif
</div>