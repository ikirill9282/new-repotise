@if (isset($paginator))
  <div class="paginator w-full">
    {{ $paginator->links('vendor.pagination.tailwind') }}
  </div>
@endif