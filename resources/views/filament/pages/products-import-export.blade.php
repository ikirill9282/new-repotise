<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Export Products</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Export products to CSV or Excel format. The export will include all filtered products from the Products list.
            </p>
            <div class="flex gap-2">
                <x-filament::button wire:click="exportCsv" color="success">
                    Export CSV
                </x-filament::button>
                <x-filament::button wire:click="exportExcel" color="success">
                    Export Excel
                </x-filament::button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Import Products</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Import products from CSV or Excel file. Required fields: title, price, category, seller.
            </p>
            <form wire:submit="import">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit" color="primary">
                        Import Products
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>




