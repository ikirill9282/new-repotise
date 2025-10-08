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
      'refund_policy' => 90,
      'subscription' => false,
    ];

    public Collection|array $types = [];
    public Collection|array $locations = [];
    public Collection|array $categories = [];

    public $subprice = [
      'month' => null,
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
          'month' => $product->subprice->month,
          'quarter' => $product->subprice->quarter,
          'year' => $product->subprice->year,
        ];
      }
    }

    public function draft()
    {
      $this->fields['status_id'] = 6;
      $this->sumbit();
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
        if (!Location::where('slug', $loc['key'])->exists()) {
          Location::create(['title' => $loc['label']]);
        }
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
        if (!Category::where('slug', $cat['key'])->exists()) {
          Category::create(['title' => $cat['label']]);
        }
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

      $data['text'] = ($data['text'] == '<h3><br></h3>' || $data['text'] == '<p><br></p>') 
        ? '' : 
        $this->processText($data['text'] ?? '');

      $data['user_id'] = $this->attrs['user_id']
        ? Crypt::decrypt($this->attrs['user_id'])
        : Auth::user()->id;

      $data['types'] = is_array($this->types) ? $this->types : $this->types->toArray();
      $data['locations'] = is_array($this->locations) ? $this->locations : $this->locations->toArray();
      $data['categories'] = is_array($this->categories) ? $this->categories : $this->categories->toArray();

      $subprice = array_map(function($elem) {
        /** @var string $elem */
        return str_ireplace('%', '', $elem);
      }, $this->subprice);
      
      $data = array_merge($data, $subprice);

      $data['price'] = str_ireplace('$', '', $data['price']);
      $data['sale_price'] = str_ireplace('$', '', $data['sale_price']);

      return $data;
    }

    public function submit()
    {
      $data = $this->prepareFormData();
      
      $validator = Validator::make($data, [
        'user_id' => 'required|integer',
        'title' => 'required|string',
        'text' => 'required|string',
        'refund_policy' => 'required|integer',
        'subscription' => 'required|boolean',
        'price' => 'required|numeric',
        'sale_price' => 'sometimes|nullable|numeric',
        'seo_title' => 'sometimes|nullable|string',
        'seo_text' => 'sometimes|nullable|string',
        'types' => 'sometimes|nullable|array',
        'locations' => 'sometimes|nullable|array',
        'categories' => 'sometimes|nullable|array',
        'month' => 'required_if:subscription,true|numeric',
        'quarter' => 'required_if:subscription,true|numeric',
        'year' => 'required_if:subscription,true|numeric',
      ], [
        'month' => [
          'required_if' => 'The month field is required when product is subscription.'
        ],
        'quarter' => [
          'required_if' => 'The month field is required when product is subscription.'
        ],
        'year' => [
          'required_if' => 'The month field is required when product is subscription.'
        ],
      ]);

      if ($validator->fails()) {
        dd($validator->errors(), $data);
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
            'month' => $valid['month'],
            'quarter' => $valid['quarter'],
            'year' => $valid['year'],
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
