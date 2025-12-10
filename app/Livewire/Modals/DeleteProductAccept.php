<?php

namespace App\Livewire\Modals;

use Livewire\Component;
use Livewire\Attributes\On;

class DeleteProductAccept extends Component
{
    public function mount()
    {
        // Предотвращаем автоматическое закрытие модального окна
        // Это модальное окно должно закрываться только по действию пользователя
    }
    
    public function render()
    {
        return view('livewire.modals.delete-product-accept');
    }
}

