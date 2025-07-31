@props([
  'id' => null,
  'user_id' => null,
  'variables' => collect([]),
])
<div class="right_edit h-0 transition overflow-hidden" id="editor-{{ $id }}"
  data-comment="{{ $id }}">
  <a href="#">{{ print_var('comment_report_message', $variables) }}</a>

  @if (auth()->user()->id == $user_id || auth()->user()->hasRole('admin'))
      <a href="#">{{ print_var('comment_edit_message', $variables) }}</a>
  @endif

  @if (auth()->user()->hasRole('admin'))
      <a href="#">{{ print_var('comment_delete_message', $variables) }}</a>
  @endif
</div>