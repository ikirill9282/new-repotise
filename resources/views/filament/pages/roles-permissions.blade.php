<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold mb-4">Roles</h3>
            {{ $this->rolesTable }}
        </div>
        
        <div>
            <h3 class="text-lg font-semibold mb-4">Permissions</h3>
            {{ $this->permissionsTable }}
        </div>
    </div>
</x-filament-panels::page>




