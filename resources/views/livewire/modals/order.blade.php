<div class="">
  {{-- HEADER --}}
  <div class="!text-2xl md:!text-3xl font-semibold !mb-8">Order # [Order ID]</div>


  {{-- CUSTOMER --}}
  <div class="!text-xl md:!text-2xl font-semibold !mb-4">Customer Information:</div>
  <div class="flex flex-col items-stretch justify-start !gap-4">
    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Customer Username:</div>
      <div class="">[Username покупателя]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Customer Email:</div>
      <div class="">[Email покупателя]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Stripe Customer ID:</div>
      <div class="">[Stripe Customer ID]</div>
    </div>
  </div>

  {{-- LINE --}}
  <div class="pb-6 mb-4 border-b-1 border-gray/30"></div>

  {{-- ORDER --}}
  <div class="!text-xl md:!text-2xl font-semibold !mb-4">Financial Breakdown:</div>
  <div class="flex flex-col items-stretch justify-start !gap-4 !pb-4 sm:!pb-0">
    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Discounts Applied:</div>
      <div class="">[скидка если применимо]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Tax Collected:</div>
      <div class="">[налоги если применимо]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">TrekGuider Commission:</div>
      <div class="">[комиссия платформы в % и в $]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Stripe Fees Breakdown:</div>
      <div class="">(общая сумма в % и в $)</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Payment Processing Fee:</div>
      <div class="">[комиссия stripe за эквайринг, зависит от платежного метода и гео]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Currency Conversion Fee:</div>
      <div class="">[конвертация валют stripe если применимо]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">International Card Fee:</div>
      <div class="">[комиссия за международные карты stripe если применимо]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Payment Method:</div>
      <div class="">[Credit Card, PayPal, etc.]</div>
    </div>

    <div class="flex flex-col items-stretch justify-start !gap-2">
      <div class="text-gray">Transaction ID (Stripe):</div>
      <div class="">[Stripe Transaction ID]</div>
    </div>
  </div>
</div>
