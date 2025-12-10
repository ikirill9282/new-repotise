<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Article;
use App\Enums\Status;

class DeleteArticle extends Component
{
    public string $article_id;

    public function mount(string $article_id)
    {
      $this->article_id = $article_id;
    }

    public function getArticle(): ?Article
    {
      try {
        $id = Crypt::decrypt($this->article_id);
        $article = Article::find($id);
        
        // Проверяем, что статья принадлежит текущему пользователю
        if ($article && $article->user_id !== Auth::id()) {
          return null;
        }
        
        return $article;
      } catch (\Exception $e) {
        return null;
      }
    }

    public function deleteArticle()
    {
      $article = $this->getArticle();
      
      if (!$article) {
        $this->dispatch('closeModal');
        return;
      }

      // Проверяем, что статья принадлежит текущему пользователю
      if ($article->user_id !== Auth::id()) {
        $this->dispatch('closeModal');
        return;
      }

      // Устанавливаем статус DELETED вместо физического удаления
      $article->status_id = Status::DELETED;
      $article->save();

      // Удаляем статью из поискового индекса
      $article->unsearchable();

      // Закрываем модальное окно
      $this->dispatch('closeModal');
      
      // Открываем модальное окно подтверждения после закрытия предыдущего
      $this->dispatch('openModalAfterClose', 'delete-article-accept');
    }

    public function render()
    {
        return view('livewire.modals.delete-article');
    }
}

