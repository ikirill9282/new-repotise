<div>
  <div class="cards_monthly_group">
      <div class="card_monthly ">
          <h3>Monthly</h3>
          <p class="text-center">{{ currency($product->month()) }} / month</p>
          <div class="subscribe justify-center items-end">
              <div class="flex flex-col gap-1">
                <a wire:click.prevent="moveCheckout('month')" href="#">Subscribe</a>
                <span>Perfect for trying it out</span>
              </div>
          </div>
      </div>
      <div class="card_monthly ">
          <h3>Quarterly</h3>
          <p class="text-center">{{ currency($product->quarter()) }} / month</p>
          <div class="subscribe justify-center items-end">
              <div class="flex flex-col gap-1">
                <a wire:click.prevent="moveCheckout('quarter')" href="#">Subscribe</a>
                <span>Billed Quarterly</span>
              </div>
          </div>
      </div>
      <div class="card_monthly ">
          <h3>Yearly</h3>
          <p class="text-center">{{ currency($product->year()) }} / month</p>
          <div class="subscribe justify-center items-end">
              <div class="flex flex-col gap-1">
                <a wire:click.prevent="moveCheckout('year')" href="#">Subscribe</a>
                <span>Billed Annually</span>
              </div>
          </div>
          <span class="best_value !h-6 !leading-5 !px-3 bg-active after:content-[''] after:absolute after:top-0 after:right-0 after:border-12 after:border-active after:!border-r-transparent after:translate-x-[100%]">BEST VALUE</span>
      </div>
  </div>
</div>
