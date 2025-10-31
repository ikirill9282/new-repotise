<div>
  @if (session()->has('status'))
    <div class="mb-4 rounded border border-emerald-500 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
      {{ session('status') }}
    </div>
  @endif

  <form wire:submit.prevent="save" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
    <div class="space-y-6">
      <x-card size="sm">
        <div class="flex flex-col gap-6">
          <div class="flex flex-col lg:flex-row items-start gap-4">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden bg-light flex-shrink-0">
              <img src="{{ $user->avatar }}" alt="Avatar" class="object-cover w-full h-full">
            </div>
            <div class="flex-1 space-y-4">
              <x-form.input 
                label="Display Name"
                name="full_name"
                :value="$full_name"
                wire:model.defer="full_name"
                class="!w-full"
              />

              <div class="flex flex-wrap gap-3">
                <label class="flex items-center gap-2 bg-light py-2 px-4 rounded cursor-pointer">
                  <input type="checkbox" class="accent-active" wire:model.defer="collaboration">
                  <span class="text-sm sm:text-base">Open for Collaboration</span>
                </label>
                <div class="min-w-[200px]">
                  <label class="text-gray block mb-2">Country</label>
                  <select wire:model.defer="country_id" class="bg-light px-3 py-2 rounded w-full outline-0">
                    <option value="">Select country</option>
                    @foreach($countries as $country)
                      <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div>
            <label class="text-gray block mb-2">Bio</label>
            <textarea
              wire:model.defer="description"
              rows="8"
              class="w-full bg-light rounded px-3 py-2 outline-0"
              placeholder="Tell fans about yourself"
            ></textarea>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <x-form.input
              label="Primary Contact"
              name="contact"
              :value="$contact"
              wire:model.defer="contact"
            />
            <x-form.input
              label="Secondary Contact"
              name="contact2"
              :value="$contact2"
              wire:model.defer="contact2"
            />
          </div>
        </div>
      </x-card>

      <x-card size="sm">
        <div class="flex justify-between items-center mb-4">
          <h2 class="font-bold text-xl">Products ({{ $stats['products'] ?? 0 }})</h2>
          <x-link href="{{ route('profile.products') }}" class="!border-0">Manage</x-link>
        </div>
        <div class="flex flex-col divide-y divide-gray/20">
          @forelse($products as $product)
            <div class="py-3 flex items-start gap-4">
              <div class="w-16 h-16 rounded overflow-hidden bg-light flex-shrink-0">
                @if($product->preview)
                  <img src="{{ $product->preview->image }}" alt="{{ $product->title }}" class="object-cover w-full h-full">
                @else
                  <div class="text-xs text-gray flex items-center justify-center w-full h-full">No image</div>
                @endif
              </div>
              <div class="flex-1 min-w-0">
                <div class="font-medium truncate">{{ $product->title }}</div>
                <div class="text-sm text-gray">Updated {{ optional($product->updated_at)->diffForHumans() }}</div>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <x-link :href="$product->makeEditUrl()" class="!border-0">Edit</x-link>
                <span class="text-gray">|</span>
                <x-link :href="$product->makeUrl()" class="!border-0" target="_blank">View</x-link>
              </div>
            </div>
          @empty
            <div class="py-4 text-gray text-sm text-center">You have not published any products yet.</div>
          @endforelse
        </div>
        <div class="text-center mt-4">
          <x-btn href="{{ route('profile.products.create') }}" class="!px-6">Add Product</x-btn>
        </div>
      </x-card>

      <x-card size="sm">
        <div class="flex justify-between items-center mb-4">
          <h2 class="font-bold text-xl">Travel Insights ({{ $stats['articles'] ?? 0 }})</h2>
          <x-link href="{{ route('profile.articles') }}" class="!border-0">Manage</x-link>
        </div>
        <div class="flex flex-col divide-y divide-gray/20">
          @forelse($articles as $article)
            <div class="py-3 flex items-start gap-4">
              <div class="w-16 h-16 rounded overflow-hidden bg-light flex-shrink-0">
                @if($article->preview)
                  <img src="{{ $article->preview->image }}" alt="{{ $article->title }}" class="object-cover w-full h-full">
                @else
                  <div class="text-xs text-gray flex items-center justify-center w-full h-full">No image</div>
                @endif
              </div>
              <div class="flex-1 min-w-0">
                <div class="font-medium truncate">{{ $article->title }}</div>
                <div class="text-sm text-gray">Updated {{ optional($article->updated_at)->diffForHumans() }}</div>
              </div>
              <div class="flex items-center gap-2 text-sm">
                <x-link :href="$article->makeEditUrl()" class="!border-0">Edit</x-link>
                <span class="text-gray">|</span>
                <x-link :href="$article->makeFeedUrl()" class="!border-0" target="_blank">View</x-link>
              </div>
            </div>
          @empty
            <div class="py-4 text-gray text-sm text-center">No travel insights yet.</div>
          @endforelse
        </div>
        <div class="text-center mt-4">
          <x-btn href="{{ route('profile.articles.create') }}" class="!px-6">Add Insight</x-btn>
        </div>
      </x-card>
    </div>

    <div class="space-y-6">
      <x-card size="sm">
        <div class="flex flex-col gap-3">
          <div class="flex gap-2">
            <x-btn type="submit" class="!w-full">Save</x-btn>
            <x-btn type="button" wire:click="cancel" outlined class="!w-full">Cancel</x-btn>
          </div>
          <p class="text-gray text-sm">
            Update your public creator profile. Changes go live immediately after saving.
          </p>
        </div>
      </x-card>

      <x-card size="sm">
        <h3 class="font-semibold mb-4">Profile Metrics</h3>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray">Followers</span><span>{{ number_format($stats['followers'] ?? 0) }}</span></div>
          <div class="flex justify-between"><span class="text-gray">Products</span><span>{{ number_format($stats['products'] ?? 0) }}</span></div>
          <div class="flex justify-between"><span class="text-gray">Travel Insights</span><span>{{ number_format($stats['articles'] ?? 0) }}</span></div>
          <div class="flex justify-between"><span class="text-gray">Donations Received</span><span>{{ currency($stats['donations'] ?? 0) }}</span></div>
        </div>
      </x-card>

      <x-card size="sm">
        <h3 class="font-semibold mb-4">Connect Online</h3>
        <div class="space-y-3">
          @foreach($socialLabels as $key => $label)
            <div>
              <label class="text-gray block mb-1">{{ $label }}</label>
              <input
                type="text"
                wire:model.defer="social.{{ $key }}"
                class="w-full bg-light rounded px-3 py-2 text-sm outline-0"
                placeholder="Profile link or handle"
              >
            </div>
          @endforeach
        </div>
      </x-card>
    </div>
  </form>
</div>
