<div>
    @if(!$payout)
        <div class="text-center py-6">
            <p class="text-gray">Payout not found.</p>
        </div>
    @else
        {{-- HEADER --}}
        <div class="text-2xl font-semibold pb-4 border-b-1 border-gray/30">Payout Details</div>

        {{-- PAYOUT INFORMATION --}}
        <div class="py-4 border-b-1 border-gray/30">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray mb-1">Payout ID</div>
                    <div class="font-semibold">{{ $payout->payout_id ?? ('POUT-' . str_pad($payout->id, 8, '0', STR_PAD_LEFT)) }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray mb-1">Status</div>
                    @php
                        $statusColors = [
                            \App\Models\Payout::STATUS_PENDING => 'text-yellow-600',
                            \App\Models\Payout::STATUS_PROCESSING => 'text-blue-600',
                            \App\Models\Payout::STATUS_IN_TRANSIT => 'text-blue-500',
                            \App\Models\Payout::STATUS_PAID => 'text-green-600',
                            \App\Models\Payout::STATUS_COMPLETED => 'text-green-600',
                            \App\Models\Payout::STATUS_REJECTED => 'text-red-600',
                            \App\Models\Payout::STATUS_FAILED => 'text-red-600',
                            \App\Models\Payout::STATUS_CANCELED => 'text-gray-600',
                        ];
                        $statusLabels = [
                            \App\Models\Payout::STATUS_PENDING => 'Pending',
                            \App\Models\Payout::STATUS_PROCESSING => 'Processing',
                            \App\Models\Payout::STATUS_IN_TRANSIT => 'In Transit',
                            \App\Models\Payout::STATUS_PAID => 'Paid',
                            \App\Models\Payout::STATUS_COMPLETED => 'Completed',
                            \App\Models\Payout::STATUS_REJECTED => 'Rejected',
                            \App\Models\Payout::STATUS_FAILED => 'Failed',
                            \App\Models\Payout::STATUS_CANCELED => 'Canceled',
                        ];
                        $statusColor = $statusColors[$payout->status] ?? 'text-gray';
                        $statusLabel = $statusLabels[$payout->status] ?? ucfirst($payout->status);
                    @endphp
                    <div class="font-semibold {{ $statusColor }}">{{ $statusLabel }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray mb-1">Date Created</div>
                    <div class="font-semibold">
                        {{ $payout->created_at ? $payout->created_at->copy()->timezone(config('app.timezone'))->format('m.d.Y H:i') : '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray mb-1">Processed At</div>
                    <div class="font-semibold">
                        {{ $payout->processed_at ? $payout->processed_at->copy()->timezone(config('app.timezone'))->format('m.d.Y H:i') : '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray mb-1">Payout Method</div>
                    <div class="font-semibold">{{ $payout->payout_method_display ?? '—' }}</div>
                </div>

                @if($payout->stripe_payout_id)
                <div>
                    <div class="text-sm text-gray mb-1">Stripe Payout ID</div>
                    <div class="font-semibold text-xs">{{ $payout->stripe_payout_id }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- FINANCIAL DETAILS --}}
        <div class="py-4 border-b-1 border-gray/30">
            <div class="text-lg font-semibold mb-3">Financial Details</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-sm text-gray mb-1">Amount</div>
                    <div class="font-semibold text-lg">-{{ currency(abs($payout->amount)) }} {{ strtoupper($payout->currency ?? 'USD') }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray mb-1">Fees</div>
                    <div class="font-semibold">
                        {{ ($payout->fees ?? 0) > 0 ? currency($payout->fees) . ' ' . strtoupper($payout->currency ?? 'USD') : '—' }}
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="text-sm text-gray mb-1">Total Deducted</div>
                    <div class="font-semibold text-lg">
                        -{{ currency(abs($payout->total_deducted ?? ($payout->amount + ($payout->fees ?? 0)))) }} {{ strtoupper($payout->currency ?? 'USD') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- REVENUE SHARES --}}
        @if($payout->revenueShares && $payout->revenueShares->isNotEmpty())
        <div class="py-4 border-b-1 border-gray/30">
            <div class="text-lg font-semibold mb-3">Related Revenue Shares</div>
            <div class="space-y-2">
                @foreach($payout->revenueShares as $share)
                    <div class="flex justify-between items-center p-2 bg-light rounded">
                        <div>
                            @if($share->product)
                                <div class="font-semibold">{{ $share->product->title }}</div>
                                <div class="text-sm text-gray">Order ID: {{ $share->order_id }}</div>
                            @else
                                <div class="font-semibold">Donation</div>
                                <div class="text-sm text-gray">Order ID: {{ $share->order_id }}</div>
                            @endif
                        </div>
                        <div class="font-semibold">{{ currency($share->author_amount) }} {{ strtoupper($payout->currency ?? 'USD') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- FAILURE/REJECTION INFORMATION --}}
        @if($payout->failure_message || $payout->rejection_reason)
        <div class="py-4 border-b-1 border-gray/30">
            <div class="text-lg font-semibold mb-3 text-red-600">
                {{ $payout->failure_message ? 'Failure Information' : 'Rejection Information' }}
            </div>
            <div class="p-3 bg-red-50 rounded text-red-800">
                {{ $payout->failure_message ?? $payout->rejection_reason }}
            </div>
        </div>
        @endif

        {{-- CLOSE BUTTON --}}
        <div class="pt-4 text-center">
            <x-btn wire:click.prevent="$dispatch('closeModal')" class="!max-w-[9rem] !inline-block !py-2">Close</x-btn>
        </div>
    @endif
</div>

