<div>
    {{-- @if (empty($this->data))
      <div class="text-lg text-center">There are no sales yet.</div>
    @else
    @endif --}}

    <div class="bg-light rounded-lg px-3 py-2.5 mb-5">
        <div class="flex flex-col !gap-2 lg:!gap-0 lg:flex-row">
            <div class="mr-auto">Referral Program</div>
            <div
                class="flex flex-col sm:flex-row items-start sm:items-center !gap-2 lg:!gap-4 text-sm justify-between lg:justify-start">
                <div class="flex justify-start items-start gap-2">
                    <div class="text-gray">Referral Earnings:</div>
                    <div class="text-nowrap">{{ currency($summary['earnings'] ?? 0) }}</div>
                </div>
                <div class="flex justify-start items-start gap-2">
                    <div class="text-gray">Referrals Invited:</div>
                    <div class="text-nowrap">{{ number_format($summary['invited'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="relative overflow-x-scroll max-w-full scrollbar-custom mb-5">
        <table class="table">
            <thead>
                <tr class="">
                    <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Date & Time</th>
                    <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Username</th>
                    <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Amount</th>
                    <th class="text-nowrap font-normal !border-b-gray/15 !pb-4">Product</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < 10; $i++)
                    <tr>
                        <td class="!border-b-gray/15 !py-4 text-nowrap">10.10.2025 10:00</td>
                        <td class="!border-b-gray/15 !py-4 text-nowrap">Donor Name</td>
                        <td class="!border-b-gray/15 !py-4">$30</td>
                        <td class="!border-b-gray/15 !py-4 text-nowrap">A Guide to Getting to Know North Korea</td>
                    </tr>
                @endfor
            </tbody>
            <tfoot></tfoot>
        </table>
    </div> --}}

    <div class="flex items-end">
        <div class="link basis-1/2">
            <span class="inline-block text-gray mb-2">
                Your Referral Link
            </span>
            <input type="hidden" value="{{ $user->makeReferalUrl() }}" data-copyId="referal" readonly />
            <div class="hover:cursor-pointer flex justify-start items-start gap-3 p-2.5 bg-light rounded-lg break-all copyToClipboard" data-target="referal">
                <span>{{ $user->makeReferalUrl() }}</span>
                <span class="text-gray">@includeIf('icons.copy')</span>
            </div>
        </div>
        <div class="socials basis-1/2 mb-2">
            <ul class="flex justify-end flex-wrap gap-3">
                <li class="!mr-0">
                    <a href="{{ $user->makeReferalUrl('FB') }}" target="_blank"
                        class="transition !text-second hover:!text-blue-600">
                        @include('icons.facebook')
                    </a>
                </li>
                <li class="!mr-0">
                    <a href="{{ $user->makeReferalUrl('PI') }}" class="transition !text-second hover:!text-rose-600"
                        target="_blank">
                        @include('icons.pinterest')
                    </a>
                </li>
                <li class="!mr-0">
                    <a href="{{ $user->makeReferalUrl('TW') }}" target="_blank"
                        class="transition !text-second hover:!text-gray-900">
                        @include('icons.twitter')
                    </a>
                </li>
                <li class="!mr-0">
                    <a href="{{ $user->makeReferalUrl('GM') }}" class="transition !text-second hover:!text-orange-600"
                        target="_blank">
                        @include('icons.mail')
                    </a>
                </li>
                <li class="!mr-0">
                    <a href="{{ $user->makeReferalUrl('WA') }}"
                        class="transition !text-second hover:!text-emerald-600" target="_blank">
                        @include('icons.whatsapp')
                    </a>
                </li>
                <li class="!mr-0">
                    <a href="{{ $user->makeReferalUrl('TG') }}" class="transition !text-second hover:!text-sky-600"
                        target="_blank">
                        @include('icons.telegram')
                    </a>
                </li>
            </ul>
        </div>
    </div>


    <div class="text-right mt-4">
        <x-btn href="{{ route('referal') }}" outlined class="!border-active hover:!border-second !w-auto !px-12">
          Learn More
        </x-btn>
    </div>
</div>
