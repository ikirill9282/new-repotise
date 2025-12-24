<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\Product as ModelProdct;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Type;
use App\Enums\Status;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Gallery;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Product extends Component
{

    use HasForm;


    public array $fields = [];

    protected array $default = [
      'refund_policy' => 30,
      'subscription' => false,
    ];

    public Collection|array $types = [];
    public Collection|array $locations = [];
    public Collection|array $categories = [];

    public $subprice = [
      'quarter' => null,
      'year' => null,
    ];

    public int $step = 1;
    public $editMode = false;
    
    public $attrs = [];

    public function mount(?string $product_id, array $default = [])
    {
      $model = is_null($product_id) ? new ModelProdct() : ModelProdct::find(Crypt::decrypt($product_id));
      $this->editMode = $model->exists;
      $this->prepareFormFields($model, $default);
    }


    public function prepareFormFields(ModelProdct $product, array $default = [])
    {
      $this->default = empty($default) ? $this->default : $default;
      $fields = $this->getFormFields($product, ['created_at', 'updated_at']);

      $this->attrs['id'] = $fields['id'] ? Crypt::encrypt($fields['id']) : null;
      $this->attrs['user_id'] = Crypt::encrypt(($fields['user_id'] ?? Auth::user()->id));

      unset($fields['user_id'], $fields['id']);

      $this->fields = array_merge($fields, $this->default);

      if ($product->types?->isNotEmpty()) {
        $this->types = $product->types->map(fn($type) => [
          'key' => $type->slug,
          'label' => $type->title,
        ]);
      }


      if ($product->locations?->isNotEmpty()) {
        $this->locations = $product->locations->map(fn($type) => [
          'key' => $type->slug,
          'label' => $type->title,
        ]);
      }

      if ($product->categories?->isNotEmpty()) {
        $this->categories = $product->categories->map(fn($type) => [
          'key' => $type->slug,
          'label' => $type->title,
        ]);
      }

      if ($product->subscription) {
        $this->subprice = [
          'quarter' => $product->subprice->quarter ?? null,
          'year' => $product->subprice->year ?? null,
        ];
      }
    }

    /**
     * Автоматическое заполнение SEO полей при изменении заголовка или описания продукта
     */
    public function updated($propertyName)
    {
      // Автоматическое заполнение SEO заголовка при изменении заголовка продукта
      if ($propertyName === 'fields.title') {
        $value = $this->fields['title'] ?? '';
        // Заполняем SEO заголовок только если он пустой
        if (empty($this->fields['seo_title']) && !empty($value)) {
          $this->fields['seo_title'] = mb_substr($value, 0, 60);
        }
      }

      // Автоматическое заполнение SEO описания при изменении описания продукта
      if ($propertyName === 'fields.text') {
        $value = $this->fields['text'] ?? '';
        // Заполняем SEO описание только если оно пустое
        if (empty($this->fields['seo_text']) && !empty($value)) {
          // Убираем HTML теги и получаем чистый текст
          // Убеждаемся что value - это строка, а не массив
          $valueString = is_array($value) ? '' : (string)$value;
          $plainText = strip_tags($valueString);
          $plainText = html_entity_decode($plainText, ENT_QUOTES, 'UTF-8');
          $plainText = preg_replace('/\s+/', ' ', trim($plainText));
          $this->fields['seo_text'] = mb_substr($plainText, 0, 160);
        }
      }
    }

    public function draft()
    {
      $this->fields['status_id'] = 6;
      $this->submit();
    }

    public function resetTypes(ModelProdct $model): void
    {
      $input = is_array($this->types) ? $this->types : $this->types->toArray();
      if (!empty($input)) {
        $types = Type::whereIn('slug', array_column($input, 'key'))
          ->pluck('id')
          ->toArray()
          ;
        $model->types()->sync($types);
      }
    }

    public function resetLocations(ModelProdct $model) 
    {
      $input = is_array($this->locations) ? $this->locations : $this->locations->toArray();
      foreach ($input as $loc) {
        // Use firstOrCreate to avoid duplicate entry errors
        // Check by title first since that's the unique constraint
        Location::firstOrCreate(
          ['title' => $loc['label']],
          ['title' => $loc['label']]
        );
      }

      if (!empty($input)) {
        $locations = Location::whereIn('slug', array_column($input, 'key'))
          ->pluck('id')
          ->toArray()
          ;
        $model->locations()->sync($locations);
      }
    }

    public function resetCategories(ModelProdct $model) 
    {
      $input = is_array($this->categories) ? $this->categories : $this->categories->toArray();
      foreach ($input as $cat) {
        // Use firstOrCreate to avoid duplicate entry errors
        // Check by title first since that's the unique constraint
        Category::firstOrCreate(
          ['title' => $cat['label']],
          ['title' => $cat['label']]
        );
      }

      if (!empty($input)) {
        $categories = Category::whereIn('slug', array_column($input, 'key'))
          ->pluck('id')
          ->toArray()
          ;
        $model->categories()->sync($categories);
      }
    }

    public function resetImages(ModelProdct $model): void
    {
      Gallery::where('model_id', $model->id)
        ->where('preview', 0)
        ->where('placement', 'text')
        ->update(['expires_at' => Carbon::now()])
        ;
      
      preg_match_all('/<img\s*src=\"(.*?)\".*?>/is', $model->text, $images);
      
      if (!empty($images[0])) {
        foreach($images[1] as $image) {
          $img = Gallery::where([
            'model_id' => 0,
            'image' => $image, 
            'placement' => 'text',
          ])
          ->first();

          if ($img) {
            $img->update(['model_id' => $model->id, 'expires_at' => null]);
          }
        }
      }
    }

    public function prepareFormData(): array
    {
      $data = $this->fields ?? [];
      $data['status_id'] = isset($data['status_id']) ? $data['status_id'] : 3;
      
      // Set default refund_policy if not provided
      if (!isset($data['refund_policy']) || $data['refund_policy'] === '' || $data['refund_policy'] === null) {
        $data['refund_policy'] = $this->default['refund_policy'] ?? 30;
      }

      // Логируем исходный текст для отладки
      $originalText = $data['text'] ?? '';
      Log::info('Product text before processing', [
        'length' => strlen($originalText),
        'preview' => substr($originalText, 0, 500),
        'full_text' => $originalText
      ]);

      $data['text'] = ($data['text'] == '<h3><br></h3>' || $data['text'] == '<p><br></p>') 
        ? '' : 
        $this->processText($data['text'] ?? '');
      
      // Логируем текст после processText
      Log::info('Product text after processText', [
        'length' => strlen($data['text']),
        'preview' => substr($data['text'], 0, 500),
        'full_text' => $data['text']
      ]);

      $data['user_id'] = $this->attrs['user_id']
        ? Crypt::decrypt($this->attrs['user_id'])
        : Auth::user()->id;

      $data['types'] = is_array($this->types) 
        ? $this->types 
        : ($this->types instanceof Collection ? $this->types->toArray() : []);
      $data['locations'] = is_array($this->locations) 
        ? $this->locations 
        : ($this->locations instanceof Collection ? $this->locations->toArray() : []);
      $data['categories'] = is_array($this->categories) 
        ? $this->categories 
        : ($this->categories instanceof Collection ? $this->categories->toArray() : []);

      $subprice = array_map(function($elem) {
        /** @var string $elem */
        $cleaned = str_ireplace('%', '', $elem);
        return $cleaned === '' ? null : $cleaned;
      }, $this->subprice);
      
      $data = array_merge($data, $subprice);

      // Map old_price to sale_price if it exists
      if (isset($data['old_price'])) {
        $data['sale_price'] = $data['old_price'];
        unset($data['old_price']);
      }

      $data['price'] = str_ireplace('$', '', $data['price'] ?? '');
      $data['sale_price'] = str_ireplace('$', '', $data['sale_price'] ?? '');

      // Convert empty strings to null for numeric fields
      if ($data['price'] === '' || $data['price'] === null) {
        $data['price'] = null;
      }
      
      if ($data['sale_price'] === '' || $data['sale_price'] === null) {
        $data['sale_price'] = null;
      }

      // Convert empty strings to null for subscription prices when subscription is false
      if (!($data['subscription'] ?? false)) {
        $data['quarter'] = null;
        $data['year'] = null;
      } else {
        // For subscription products, convert empty strings to null
        if (isset($data['quarter']) && $data['quarter'] === '') {
          $data['quarter'] = null;
        }
        if (isset($data['year']) && $data['year'] === '') {
          $data['year'] = null;
        }
      }

      // Автоматическое заполнение SEO полей, если они пустые
      if (empty($data['seo_title']) && !empty($data['title'])) {
        $data['seo_title'] = mb_substr($data['title'], 0, 60);
      }
      
      if (empty($data['seo_text']) && !empty($data['text'])) {
        // Убираем HTML теги и получаем чистый текст
        $plainText = strip_tags($data['text']);
        $plainText = html_entity_decode($plainText, ENT_QUOTES, 'UTF-8');
        $plainText = preg_replace('/\s+/', ' ', trim($plainText));
        $data['seo_text'] = mb_substr($plainText, 0, 160);
      }

      return $data;
    }

    public function submit()
    {
      $data = $this->prepareFormData();
      
      $validator = Validator::make($data, [
        'user_id' => 'required|integer',
        'title' => ['required', 'string', 'max:70', "regex:/^[a-zA-Z0-9\s\-'.,!?():;]+$/"],
        'text' => 'required|string',
        'refund_policy' => 'nullable|integer',
        'subscription' => 'required|boolean',
        'price' => 'required|numeric',
        'sale_price' => 'sometimes|nullable|numeric',
        'seo_title' => 'sometimes|nullable|string|max:70',
        'seo_text' => 'sometimes|nullable|string|max:160',
        'types' => 'required|array|min:1',
        'locations' => 'required|array|min:1',
        'categories' => 'required|array|min:1',
        'quarter' => 'sometimes|nullable|numeric',
        'year' => 'sometimes|nullable|numeric',
      ], [
        'title.required' => 'The title field is required.',
        'title.regex' => 'The title must contain only Latin letters, numbers, spaces, and basic punctuation marks (no Cyrillic or other characters).',
        'text.required' => 'The product description field is required.',
        'price.required' => 'The price field is required.',
        'types.required' => 'Please select at least one product type.',
        'types.min' => 'Please select at least one product type.',
        'locations.required' => 'Please select at least one location.',
        'locations.min' => 'Please select at least one location.',
        'categories.required' => 'Please select at least one category.',
        'categories.min' => 'Please select at least one category.',
      ]);

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();

      $attributes = collect($valid)->only([
        'user_id',
        'title',
        'text',
        'subscription',
        'price',
        'sale_price',
        'refund_policy',
        'seo_title',
        'seo_text',
      ])->toArray();
      

      DB::beginTransaction();
      try {

        if ($this->attrs['id']) {
          $model = ModelProdct::find(Crypt::decrypt($this->attrs['id']));
          $model->update($attributes);
        } else {
          $model = ModelProdct::create($attributes);
        }

        $this->resetTypes($model);
        $this->resetLocations($model);
        $this->resetCategories($model);
        $this->resetImages($model);

        if ($model->subscription) {

          $model->subprice()->updateOrCreate(
          ['product_id' => $model->id],
          [
            'month' => null,
            'quarter' => $valid['quarter'] ?? null,
            'year' => $valid['year'] ?? null,
          ]);
        } 

      } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error while saving product', [
          'route' => request()->route()->name,
          'data' => $valid,
          'error' => $e,
        ]);

        $this->dispatch('toastError', ['message' => 'Something went wrong...']);
        return ;
      } catch (\Error $e) {
        DB::rollBack();
        Log::error('Error while saving product', [
          'route' => request()->route()->name,
          'data' => $valid,
          'error' => $e,
        ]);
        $this->dispatch('toastError', ['message' => 'Something went wrong...']);
        return ;
      }

      DB::commit();
      
      if ($model->status_id == Status::DRAFT) {
        $this->dispatch('toastSuccess', ['message' => 'Product saved as draft. Feel free to review and publish later.']);
        return ;
      }
      
      $this->dispatch('toastSuccess', ['message' => 'Success! Your product has just landed. Time to pack its bags with media content and send it off on a journey to captivate customers!']);

      return redirect($model->makeEditMediaUrl());
    }

    public function render()
    {
      return view('livewire.forms.product');
    }
}
