@if (isset($paginator))
  <div class="paginator">
    {{ $paginator->links() }}
  </div>
@endif