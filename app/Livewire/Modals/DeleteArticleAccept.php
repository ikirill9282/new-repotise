<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class DeleteArticleAccept extends Component
{
    public function mount()
    {
        // Предотвращаем автоматическое закрытие модального окна
        // Это модальное окно должно закрываться только по действию пользователя
    }
    
    public function render()
    {
        return view('livewire.modals.delete-article-accept');
    }
}

