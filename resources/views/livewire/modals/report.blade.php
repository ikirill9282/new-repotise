<div >
  <form wire:submit.prevent="submit" action="">
    <input type="hidden">
    <div class="italic p-3 bg-sky-100 rounded mb-3">{{ $this->model->text }}</div>
    <div class="mb-3">
      <textarea
        wire:model="form.message" 
        name=""
        id="" 
        class="rounded border-1 border-gray/50 p-3" 
        placeholder="Please enter your report..."
      ></textarea>
    </div>
    <div class="popUp__donateThank-buttons">
      <button @click.prevent="$dispatch('closeModal')" class="popUp__donate-fail-cancelBtn">Cancel</button>
      <button class="popUp__edit-contact-btn main-btn">Send</button>
    </div>
  </form>
</div>
