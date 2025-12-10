<?php

namespace App\Livewire\Modals;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Product;
use App\Enums\Status;

class DeleteProduct extends Component
{
    public string $product_id;

    public function mount(string $product_id)
    {
      $this->product_id = $product_id;
    }

    public function getProduct(): ?Product
    {
      try {
        $id = Crypt::decrypt($this->product_id);
        $product = Product::find($id);
        
        // Проверяем, что товар принадлежит текущему пользователю
        if ($product && $product->user_id !== Auth::id()) {
          return null;
        }
        
        return $product;
      } catch (\Exception $e) {
        return null;
      }
    }

    public function deleteProduct()
    {
      $product = $this->getProduct();
      
      if (!$product) {
        $this->dispatch('closeModal');
        return;
      }

      // Проверяем, что товар принадлежит текущему пользователю
      if ($product->user_id !== Auth::id()) {
        $this->dispatch('closeModal');
        return;
      }

      // Устанавливаем статус DELETED вместо физического удаления
      // Это позволит сохранить доступ покупателям через OrderProducts
      $product->status_id = Status::DELETED;
      $product->save();

      // Удаляем товар из поискового индекса
      $product->unsearchable();

      // Закрываем модальное окно
      $this->dispatch('closeModal');
      
      // Открываем модальное окно подтверждения после закрытия предыдущего
      // Используем JavaScript событие для открытия нового окна с задержкой
      $this->dispatch('openModalAfterClose', 'delete-product-accept');
      
      // НЕ отправляем событие products-refresh здесь, чтобы не закрывать модальное окно
      // Событие будет отправлено после закрытия модального окна delete-product-accept
    }

    public function render()
    {
        return view('livewire.modals.delete-product');
    }
}

