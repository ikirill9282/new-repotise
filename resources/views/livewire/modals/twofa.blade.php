<div>
  <div class="py-6 mb-4 border-y-1 border-gray/30">
    {{-- HEADER --}}
    <div class="text-2xl font-semibold mb-4">Two-Factor Authentication (2FA)</div>
    
    {{-- DESCRIPTION --}}
    <div class="">
      <p>Защитите аккаунт при помощи двухфакторной аутентификации.</p>
      <p>1. Отсканируйте QR-код в приложении-аутентификаторе (Google Authenticator, Authy и т.д.).</p>
      <p>2. Введите код из приложения и подтвердите, что сохранили резервные коды.</p>
    </div>
  </div>

  <div class="text-center mb-6">
    <div class="mb-4">
      @if($qrCodeSrc)
        <img class="max-w-full inline-block" src="{{ $qrCodeSrc }}" alt="QR Code">
      @else
        <div class="bg-light rounded !p-6 text-gray">Не удалось сформировать QR-код. Попробуйте обновить модальное окно.</div>
      @endif
    </div>
    <div class="text-gray mb-1">
      Ключ для ручного ввода
    </div>
    <div class="font-mono text-base sm:text-lg break-all mb-2">{{ strtoupper($manualKey ?? '') }}</div>
    <div class="flex justify-center">
      <div class="bg-light relative px-3 py-2 !pr-9 rounded flex justify-between items-center gap-3 hover:cursor-pointer copyToClipboard" data-target="twofa_setup_key">
        <input type="hidden" value="{{ strtoupper($manualKey ?? '') }}" data-copyId="twofa_setup_key" readonly />
        <span class="text-sm text-gray">Скопировать ключ</span>
        <img src="{{ asset('assets/img/copy-icon.svg') }}" alt="Copy">
        <x-tooltip message="tooltip" class="right-3"></x-tooltip>
      </div>
    </div>
    @if($otpAuthUrl)
      <div class="text-sm text-gray mt-4">
        Ссылка для добавления вручную:
        <x-link href="{{ $otpAuthUrl }}" target="_blank" class="!inline-block !ml-1 break-all">{{ $otpAuthUrl }}</x-link>
      </div>
    @endif
  </div>

  <div class="flex flex-col gap-4 pb-6 mb-4 border-y-1 border-gray/30">
    <div>
      <div class="text-gray mb-2">Резервные коды</div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        @foreach($backupCodes as $index => $backupCode)
          <div class="bg-light relative px-3 py-3 !pr-9 rounded flex justify-between items-center hover:cursor-pointer copyToClipboard" data-target="twofa_backup_{{ $index }}">
            <input type="hidden" value="{{ $backupCode }}" data-copyId="twofa_backup_{{ $index }}" readonly />
            <div class="font-mono text-base">{{ $backupCode }}</div>
            <img src="{{ asset('assets/img/copy-icon.svg') }}" alt="Copy">
            <x-tooltip message="tooltip" class="right-3"></x-tooltip>
          </div>
        @endforeach
      </div>
      <div class="text-gray text-sm mt-2">Сохраните коды в надежном месте — они понадобятся, если вы потеряете доступ к приложению-аутентификатору.</div>
    </div>

    <x-form.input 
      name="code"
      label="Verification Code" 
      placeholder="Enter Code" 
      data-input="integer"
      :tooltip="false"
      wire:model.defer="code"
    />

    <div>
      <x-form.checkbox 
        id="twofa-backup-confirm"
        name="confirmedBackup"
        wire:model="confirmedBackup"
        label="Я сохранил(-а) резервные коды"
      />
      @error('confirmedBackup')
        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
      @enderror
    </div>
  </div>

  {{-- BUTTONS --}}
  <div class="flex justify-center items-center gap-3">
    <x-btn class="!text-sm sm:!text-base !w-auto sm:!px-12" wire:click.prevent="$dispatch('closeModal')" outlined>Cancel</x-btn>
    <x-btn 
      class="!text-sm sm:!text-base !grow" 
      wire:click.prevent="enable"
      wire:loading.attr="disabled"
      wire:target="enable"
    >
      <span wire:loading.remove wire:target="enable">Enable 2FA</span>
      <span wire:loading wire:target="enable">Enabling...</span>
    </x-btn>
  </div>
</div>
