<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\On;
use App\Enums\Status;
use App\Models\Gallery;
use App\Models\Product;
use App\Traits\HasForm;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Helpers\Collapse;
use App\Models\ProductFiles;
use App\Models\ProductLinks;
use Livewire\Component;
use Mews\Purifier\Facades\Purifier;

class ProductMedia extends Component
{
    use HasForm, WithFileUploads;

    public array $fields = [
      'status_id' => null,
      'banner' => [
        'uploaded' => null,
        'preview' => null,
      ],
      'gallery' => [
        'image1' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null,
        ],
        'image2' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null,
        ],
        'image3' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null,
        ],
        'image4' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null,
        ],
        'image5' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null,
        ],
        'image6' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null,
        ],
        'image7' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null
        ],
        'image8' => [
          'uploaded' => null,
          'preview' => null,
          'id' => null
        ],
      ],
      'pp_text' => null,
      'files' => [
        'file1' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
        'file2' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
        'file3' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
        'file4' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
        'file5' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
        'file6' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
        'file7' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
        'file8' => [
          'uploaded' => null,
          'current' => null,
          'id' => null,
          'description' => null,
        ],
      ],
      'links' => [],
    ];

    public array $default = [];

    public array $attrs = [];

    public bool $editMode = false;

    public function mount(string $product_id, array $default = [])
    {
      $this->attrs['id'] = $product_id;
      $this->prepareFormFields();
    }
    
    public function draft()
    {
      $this->fields['status_id'] = Status::DRAFT;
      $this->submit();
    }

    public function prevStep()
    {
      return redirect($this->getProduct()->makeEditUrl());
    }

    public function addLink(array $link = []): void
    {
      if (count($this->fields['links']) <= 8) {
        $this->fields['links'][] = empty($link) ? [
          'link' => null,
          'id' => null,
        ] : $link;
      }
    }

    #[On('fileDescriptionUpdated')]
    public function onFileDescriptionUpdate($args)
    {
      if (isset($args['key']) && isset($args['description'])) {
        $this->fields['files'][$args['key']]['description'] = $args['description'];
      }
    }

    public function prepareFormFields(): void
    {
      $product = $this->getProduct();
      
      $this->fields['pp_text'] = $product->pp_text;
      $this->fields['status_id'] = $product->status_id;
      
      $this->attrs['user_id'] = Crypt::encrypt($product->author->id);

      if ($product->preview()->exists()) {
        $this->fields['banner']['preview'] = $product->preview->image;
      }

      if ($product->gallery->isNotEmpty()) {
        foreach ($product->gallery->where('preview', 0) as $item) {
          $key = null;
          foreach ($this->fields['gallery'] as $k => $val) {
            if($val['id'] == null) {
              $key = $k;
              break;
            }
          }
          
          if ($key) {
            $this->fields['gallery'][$key]['preview'] = $item->image;
            $this->fields['gallery'][$key]['id'] = Crypt::encrypt($item->id);
          }
        }
      }

      if ($product->links->isNotEmpty()) {

        $this->fields['links'] = $product->links->map(fn($link) => [
          'link' => $link->link,
          'id' => Crypt::encrypt($link->id),
        ])->toArray();

        $this->addLink();
      } else {
        $this->addLink();
      }
      while(count($this->fields['links']) < 3) {
        $this->addLink();
      }

      if ($product->files->isNotEmpty()) {
        foreach ($product->files as $file) {
          $key = null;
          foreach ($this->fields['files'] as $k => $f) {
            if ($f['id'] === null) {
              $key = $k;
              break;
            }
          }

          if ($key) {
            $this->fields['files'][$key]['current'] = $file->name;
            $this->fields['files'][$key]['id'] = Crypt::encrypt($file->id);
            $this->fields['files'][$key]['description'] = $file->description;
          }
        }
      }
    }

    public function prepareFormData(): array
    {
      $data = $this->fields;
      if ($data['pp_text']) $data['pp_text'] = Purifier::clean($data['pp_text']);

      $validator = Validator::make($data, [
        'banner' => 'required|array',
        'status_id' => 'required|integer',
        'gallery' => 'required|array',
        'pp_text' => 'sometimes|nullable|string',
        'files' => 'sometimes|nullable|array',
        'links' => 'sometimes|nullable|array',
      ]);

      if ($validator->fails()) {
        dd($validator->errors());
        throw new ValidationException($validator);
      }

      $valid = $validator->validated();

      if (empty($valid['banner']['uploaded']) && empty($valid['banner']['preview'])) {
        $validator->errors()->add('banner', 'The Featured Photo is required.');
        throw new ValidationException($validator);
      }

      return $valid;
    }

    public function submit()
    {
      $data = $this->prepareFormData();
      $product = $this->getProduct();
      $attributes = collect($data)->only([
        'pp_text',
        'status_id',
      ])->toArray();

      DB::beginTransaction();
      try {

        $product->update($attributes);
        
        $this->resetBanner($product, $data);
        $this->resetGallery($product, $data);
        $this->resetLinks($product, $data);
        $this->resetFiles($product, $data);

      } catch (\Exception $e) {
        DB::rollBack();
        $this->submitError($e);
        return ;  
      } catch (\Error $e) {
        DB::rollBack();
        $this->submitError($e);
        return ;
      }

      DB::commit();
      $this->dispatch('toastSuccess', ['message' => 'Product Media updated successful!']);
      // return redirect()->route('profile.products');
    }

    protected function resetBanner(Product $product, array $data)
    {
      if (!empty($data['banner']['uploaded'])) {
        $image = $data['banner']['uploaded'];
        $path = $image->store('images', 'public');

        if ($product->preview?->exists()) {
          $product->preview->update(['preview' => 0, 'expires_at' => Carbon::now()]);
        }

        Gallery::create([
          'user_id' => $product->user_id,
          'model_id' => $product->id,
          'type' => 'products',
          'image' => "/storage/$path",
          'preview' => 1,
          'placement' => 'site',
          'size' => Collapse::bytesToMegabytes($image->getSize()),
        ]);
      }
    }

    protected function resetGallery(Product $product, array $data)
    {
      foreach ($data['gallery'] as $key => $item) {
        if (!empty($item['uploaded'])) {
          
          if (!empty($item['id'])) {
            Gallery::where('id', Crypt::decrypt($item['id']))->update(['expires_at' => Carbon::now()]);
          }
          $path = $item['uploaded']->store('images', 'public');
          Gallery::create([
            'user_id' => $product->user_id,
            'model_id' => $product->id,
            'type' => 'products',
            'image' => "/storage/$path",
            'preview' => 0,
            'placement' => 'gallery',
            'size' => Collapse::bytesToMegabytes($item['uploaded']->getSize()),
          ]);
        } elseif (empty($item['preview']) && !empty($item['id'])) {
          Gallery::where('id', Crypt::decrypt($item['id']))->update(['expires_at' => Carbon::now()]);
        }
      }
    }

    protected function resetLinks(Product $product, array $data)
    {
      if (!empty($this->fields['links'])) {
        foreach ($this->fields['links'] as $item) {
          if (!empty($item['link'])) {
            if (!empty($item['id'])) {
              ProductLinks::where('id', Crypt::decrypt($item['id']))->update(['link' => $item['link']]);
            } else {
              ProductLinks::create(['link' => $item['link'], 'user_id' => $product->author->id, 'product_id' => $product->id]);
            }
          }
        }
      }
    }

    protected function resetFiles(Product $product, array $data)
    {
      foreach($data['files'] as $file) {
        if (!empty($file['uploaded'])) {
          
          if (!empty($file['id'])) {
            ProductFiles::where('id', Crypt::decrypt($file['id']))->update(['expires_at' => Carbon::now()]);
          }

          ProductFiles::create([
            'user_id' => $product->author->id,
            'product_id' => $product->id,
            'file' => $file['uploaded']->store('products'),
            'size' => Collapse::bytesToMegabytes($file['uploaded']->getSize()),
            'description' => $file['description'] ? $this->processText($file['description']) : null,
            'name' => Purifier::clean($file['uploaded']->getClientOriginalName()),
          ]);

        } elseif (empty($file['current']) && !empty($file['id'])) {
          ProductFiles::where('id', Crypt::decrypt($file['id']))->update(['expires_at' => Carbon::now()]);
        } elseif (!empty($file['description'])) {
          ProductFiles::where('id', Crypt::decrypt($file['id']))->update(['description' => $this->processText($file['description'])]);
        }
      }
    }

    protected function getProduct(): Product
    {
      return Product::where('id', Crypt::decrypt($this->attrs['id']))
        ->with('author', 'links', 'files', 'gallery')
        ->first();
    }

    public function submitError(\Exception|\Error $e)
    {
      Log::error('Error while submit product media form.', [
        'request' => request(),
        'user' => Auth::user(),
        'data' => $this->prepareFormData(),
        'error' => $e,
      ]);
      $this->dispatch('toastError', ['message' => 'Something went wrong...']);
    }
    
    public function dropPhoto(string $key)
    {
      $this->fields['gallery'][$key]['uploaded'] = null;
      $this->fields['gallery'][$key]['preview'] = null;
    }

    public function dropFile(string $key)
    {
      $this->fields['files'][$key]['uploeded'] = null;
      $this->fields['files'][$key]['current'] = null;
    }
    
    public function render()
    {
      return view('livewire.forms.product-media');
    }
}
