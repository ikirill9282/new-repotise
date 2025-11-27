<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

trait HasForm
{
    public function getValidFormState(): ?array
    {
        return $this->validate()['form'] ?? null;
    }

    public function resetForm()
    {
        $this->form = array_fill_keys(array_keys($this->form), null);
        $this->resetValidation();
    }

    public function getFormFields(Model $model, array $exclude = []): array
    {
      if ($model->exists) {
        return collect($model->getAttributes())
          ->filter(fn($val, $key) => !in_array($key, $exclude))
          ->toArray();
      } else {
        $fields = Schema::getColumnListing($model->getTable());
        $fields = collect($fields)
          ->filter(fn($item) => !in_array($item, $exclude))
          ->values()
          ->toArray();

        return array_fill_keys($fields, null);
      }
    }

    public function processText(string $text): string
    {
      // ВАЖНО: Преобразуем списки с data-list атрибутами перед Purifier
      // Quill использует data-list="bullet" для маркированных списков внутри <ol>
      // Нужно преобразовать их в правильные <ul> и <ol>
      
      // Обрабатываем <ol> с элементами, имеющими data-list="bullet" - преобразуем в <ul>
      $text = preg_replace_callback('/<ol[^>]*>(.*?)<\/ol>/is', function($matches) {
          $content = $matches[1];
          // Проверяем, есть ли элементы с data-list="bullet"
          if (preg_match('/data-list=["\']bullet["\']/i', $content)) {
              // Преобразуем <ol> в <ul> и удаляем data-list атрибуты
              $content = preg_replace('/data-list=["\'][^"\']*["\']/i', '', $content);
              $content = preg_replace('/<li[^>]*>/i', '<li>', $content);
              return '<ul>' . $content . '</ul>';
          }
          // Если data-list="ordered" или нет data-list, оставляем как <ol> и удаляем атрибуты
          $content = preg_replace('/data-list=["\'][^"\']*["\']/i', '', $content);
          $content = preg_replace('/<li[^>]*>/i', '<li>', $content);
          return '<ol>' . $content . '</ol>';
      }, $text);
      
      // Также обрабатываем <ul> с элементами, имеющими data-list="ordered" - преобразуем в <ol>
      $text = preg_replace_callback('/<ul[^>]*>(.*?)<\/ul>/is', function($matches) {
          $content = $matches[1];
          // Проверяем, есть ли элементы с data-list="ordered"
          if (preg_match('/data-list=["\']ordered["\']/i', $content)) {
              // Преобразуем <ul> в <ol> и удаляем data-list атрибуты
              $content = preg_replace('/data-list=["\'][^"\']*["\']/i', '', $content);
              $content = preg_replace('/<li[^>]*>/i', '<li>', $content);
              return '<ol>' . $content . '</ol>';
          }
          // Если data-list="bullet" или нет data-list, оставляем как <ul> и удаляем атрибуты
          $content = preg_replace('/data-list=["\'][^"\']*["\']/i', '', $content);
          $content = preg_replace('/<li[^>]*>/i', '<li>', $content);
          return '<ul>' . $content . '</ul>';
      }, $text);
      
      // Удаляем оставшиеся data-list атрибуты (на случай, если они остались)
      $text = preg_replace('/\s*data-list=["\'][^"\']*["\']/i', '', $text);
      
      // Нормализуем пробелы, но сохраняем структуру
      $text = preg_replace('/\s+/u', ' ', $text);
      
      // Удаляем только множественные пустые параграфы (3 и более подряд), но сохраняем одиночные и двойные
      $text = preg_replace('/(?:<p[^>]*>\s*<br\s*\/?>\s*<\/p>\s*){3,}/i', '<p><br></p>', $text);
      
      // Удаляем только полностью пустые параграфы без атрибутов (без текста, без <br>, без пробелов)
      $text = preg_replace('/(?:<p[^>]*>\s*<\/p>)/i', '', $text);
      
      // Удаляем стили фона и цвета из style атрибутов, но сохраняем остальные стили
      $text = preg_replace_callback('/style="([^"]*)"/is', function($matches) {
          $styles = $matches[1];
          // Удаляем background-color и color, но сохраняем остальное
          $styles = preg_replace('/(background-color|color)[^;]*;?\s*/i', '', $styles);
          $styles = trim($styles, '; ');
          return $styles ? 'style="' . $styles . '"' : '';
      }, $text);
      
      // Заменяем множественные <br> внутри параграфов на один, но сохраняем параграфы
      $text = preg_replace_callback('/(<p[^>]*>)(.*?)(<\/p>)/is', function($matches) {
          $content = $matches[2];
          // Заменяем множественные <br> на один, но сохраняем структуру
          $content = preg_replace('/(<br\s*\/?>){2,}/i', '<br>', $content);
          return $matches[1] . $content . $matches[3];
      }, $text);
      
      // Убеждаемся, что параграфы с текстом не удаляются
      // Если параграф содержит только пробелы, заменяем их на неразрывный пробел
      $text = preg_replace_callback('/(<p[^>]*>)(\s+)(<\/p>)/i', function($matches) {
          // Если параграф содержит только пробелы, оставляем его как есть (не удаляем)
          return $matches[1] . '&nbsp;' . $matches[3];
      }, $text);

      return trim($text);
    }

}