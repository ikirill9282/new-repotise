<div>
    <div class="mb-4 flex gap-4">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search by Transaction ID..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
        />
        <select wire:model.live="feeType" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
            <option value="">All Fee Types</option>
            <option value="platform">Platform Commission</option>
            <option value="referral">Referral Reward</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Transaction ID</th>
                    <th class="text-left p-2">Seller</th>
                    <th class="text-left p-2">Product</th>
                    <th class="text-right p-2">Fee Type</th>
                    <th class="text-right p-2">Amount</th>
                    <th class="text-right p-2">Rate</th>
                    <th class="text-right p-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fees as $fee)
                    <tr class="border-b">
                        <td class="p-2">
                            <a href="{{ route('filament.admin.resources.orders.index', ['tableSearch' => $fee->order_id]) }}" class="text-primary-600 hover:underline">
                                #{{ $fee->order_id }}
                            </a>
                        </td>
                        <td class="p-2">
                            @if($fee->seller_id)
                                <a href="{{ route('filament.admin.resources.users.index', ['tableSearch' => $fee->seller_id]) }}" class="text-primary-600 hover:underline">
                                    {{ $fee->seller_name ?? $fee->seller_username ?? 'N/A' }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="p-2">{{ $fee->product_title ?? 'N/A' }}</td>
                        <td class="p-2 text-right">
                            @if($fee->platform_reward > 0)
                                Platform Commission
                            @elseif($fee->referal_reward > 0)
                                Referral Reward
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="p-2 text-right">${{ number_format($fee->platform_reward + $fee->referal_reward, 2) }}</td>
                        <td class="p-2 text-right">
                            @php
                                $total = $fee->total ?? 1;
                                $feeAmount = $fee->platform_reward + $fee->referal_reward;
                                $rate = $total > 0 ? ($feeAmount / $total) * 100 : 0;
                            @endphp
                            {{ number_format($rate, 2) }}%
                        </td>
                        <td class="p-2 text-right">{{ $fee->order_date ? \Carbon\Carbon::parse($fee->order_date)->format('Y-m-d') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">
                            No data for selected period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $fees->links() }}
    </div>
</div>

