<?php

namespace App\Livewire\Forms;

use App\Enums\Status;
use Livewire\Component;
use App\Models\Article as ModelArticle;
use App\Models\ArticleTags;
use App\Models\Gallery;
use App\Models\Tag;
use App\Traits\HasForm;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Collapse;
use Mews\Purifier\Facades\Purifier;
use App\Jobs\OptimizeMedia;

class Article extends Component
{

    use HasForm, WithFileUploads;

    public $fields = [];

    /** @var Collection|array $tags */
    public $tags = [];

    public $banner;
    public $bannerPath;

    public $attrs = [];
    protected $default = [
      // 'scheduled_at' => '20/02/2025',
      // 'seo_title' => 'test',
      // 'seo_text' => 'asd123',
    ];

    public $editMode = false;
    public int $step = 1;
  
    public function mount(?string $article_id, array $default = [])
    {
      $model = is_null($article_id) ? new ModelArticle() : ModelArticle::find(Crypt::decrypt($article_id));
      $this->editMode = $model->exists;
      $this->step = 1; // Always start at step 1 for new articles
      $this->prepareFormFields($model, $default);
    }

    public function draft()
    {
      $this->fields['status_id'] = 2;
      $this->submit();
    }

    public function publish()
    {
      $this->fields['status_id'] = 3;
      $this->submit();
    }

    public function nextStep()
    {
      // Check if text is empty (just HTML tags)
      $text = ($this->fields['text'] ?? '') == '<h3><br></h3>' || ($this->fields['text'] ?? '') == '<p><br></p>' || empty($this->fields['text'] ?? '')
        ? '' 
        : $this->fields['text'] ?? '';

      // Validate step 1 required fields
      $validator = Validator::make([
        'title' => $this->fields['title'] ?? '',
        'text' => $text,
      ], [
        'title' => ['required', 'string', "regex:/^[a-zA-Z0-9\s\-'.,!?():;]+$/"],
        'text' => 'required|string',
      ], [
        'title.required' => 'The title field is required.',
        'title.regex' => 'The title must contain only Latin letters, numbers, spaces, and basic punctuation marks (no Cyrillic or other characters).',
        'text.required' => 'The article content field is required.',
      ]);

      // Check banner
      if (empty($this->bannerPath) && empty($this->banner)) {
        $validator->errors()->add('banner', 'The banner field is required');
      }

      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      // Автоматическое заполнение SEO полей из данных шага 1, если они пустые
      $this->autoFillSeoFields();

      $this->step = 2;
      $this->dispatch('stepChanged');
    }

    protected function autoFillSeoFields()
    {
      // Автоматическое заполнение SEO заголовка из заголовка статьи
      if (empty($this->fields['seo_title']) && !empty($this->fields['title'])) {
        $this->fields['seo_title'] = mb_substr($this->fields['title'], 0, 70);
      }

      // Автоматическое заполнение SEO описания из текста статьи
      if (empty($this->fields['seo_text']) && !empty($this->fields['text'])) {
        // Убираем HTML теги и получаем чистый текст
        $plainText = strip_tags($this->fields['text']);
        $plainText = html_entity_decode($plainText, ENT_QUOTES, 'UTF-8');
        $plainText = preg_replace('/\s+/', ' ', trim($plainText));
        $this->fields['seo_text'] = mb_substr($plainText, 0, 160);
      }
    }

    public function prevStep()
    {
      $this->step = 1;
      $this->dispatch('stepChanged');
    }

    public function removeBanner()
    {
      $this->banner = null;
      $this->bannerPath = null;
      $this->dispatch('bannerRemoved');
    }

    public function prepareFormFields(ModelArticle $article, array $default = [])
    {
      $this->default = empty($default) ? $this->default : $default;
      $fields = $this->getFormFields($article, ['created_at', 'updated_at']);
      if ($fields['scheduled_at']) $fields['scheduled_at'] = Carbon::parse($fields['scheduled_at'])->format('m/d/Y');

      $this->attrs['id'] = $fields['id'] ? Crypt::encrypt($fields['id']) : null;
      $this->attrs['user_id'] = Crypt::encrypt(($fields['user_id'] ?? Auth::user()->id));

      unset($fields['user_id'], $fields['id']);

      $this->fields = array_merge($fields, $this->default);
      if ($article->tags?->isNotEmpty()) {
        $this->tags = $article->tags->map(fn($tag) => [
          'key' => $tag->slug,
          'label' => $tag->title,
        ]);
      }

      $this->bannerPath = $article?->preview->image ?? '';
    }

    public function submit()
    {
      // Keep previous UX: when SEO fields are empty, auto-fill them from title/content.
      $this->autoFillSeoFields();

      $data = $this->fields;
      $data['status_id'] = isset($data['status_id']) ? $data['status_id'] : 3;
      $data['text'] = ($data['text'] == '<h3><br></h3>' || $data['text'] == '<p><br></p>')
        ? '' 
        : $data['text'];
      $data['user_id'] = $this->attrs['user_id']
        ? Crypt::decrypt($this->attrs['user_id'])
        : Auth::user()->id;

      $data['scheduled_at'] = $data['scheduled_at'] 
        ? Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:s')
        : null;
      
      $validator = Validator::make($data, [
        'user_id' => 'required|integer',
        'status_id' => 'sometimes|nullable|integer',
        'title' => ['required', 'string', "regex:/^[a-zA-Z0-9\s\-'.,!?():;]+$/"],
        'text' => 'required|string',
        'seo_title' => 'sometimes|nullable|string',
        'seo_text' => 'sometimes|nullable|string',
        'scheduled_at' => 'sometimes|nullable|string',
        'banner' => 'sometimes|nullable',
      ], [
        'title.regex' => 'The title must contain only Latin letters, numbers, spaces, and basic punctuation marks (no Cyrillic or other characters).',
      ]);

      if ($validator->fails()) {
        // dd($validator->errors());
        throw new ValidationException($validator);
      }

      if (empty($this->bannerPath) && empty($this->banner)) {
        $validator->errors()->add('banner', 'The banner field is required');
        throw new ValidationException($validator);
      }

      // Validate banner size if uploaded
      if ($this->banner) {
        $bannerValidator = Validator::make(['banner' => $this->banner], [
          'banner' => 'image|mimes:jpeg,jpg,png,gif,webp|max:102400',
        ]);
        if ($bannerValidator->fails()) {
          $validator->errors()->merge($bannerValidator->errors());
          throw new ValidationException($validator);
        }
      }

      $valid = $validator->validated();
      $valid['text'] = $this->processText($valid['text']);

      DB::beginTransaction();
      try {
        if ($this->attrs['id']) {
          $model = ModelArticle::find(Crypt::decrypt($this->attrs['id']));
          $model->update($valid);
        } else {
          $model = ModelArticle::create($valid);
        }

        $this->resetTags($model);
        $this->resetBanner($model);
        $this->resetImages($model);

      } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error while edititng article', ['data' => $valid, 'error' => $e]);
        $this->dispatch('toastError', ['message' => 'Something went wrong...']);
        return ;
      } catch (\Error $e) {
        DB::rollBack();
        Log::error('Error while edititng article', ['data' => $valid, 'error' => $e]);
        $this->dispatch('toastError', ['message' => 'Something went wrong...']);
        return ;
      }

      DB::commit();

      $text = $this->editMode ? 'updated' : 'created';
      $this->dispatch('toastSuccess', ['message' => "Article $text successful!"]);
      
      // Always redirect to articles page after creation
      return redirect()->route('profile.articles');
    }

    public function resetBanner(ModelArticle $model): void
    {
      try {
        if ($this->banner) {
          if ($model->preview?->exists()) {
            $model->preview->update(['preview' => 0, 'expires_at' => Carbon::now()]);
          }
          $path = $this->banner->store('images', 'public');
          $model->gallery()->create([
            'user_id' => $model->user_id,
            'type' => 'articles',
            'image' => "/storage/$path",
            'preview' => 1,
            'size' => Collapse::bytesToMegabytes($this->banner->getSize()),
          ]);
          OptimizeMedia::dispatch('public', $path);
        }
      } catch (\Exception $e) {
        if (isset($path)) Storage::disk('public')->delete(str_ireplace(('/storage' . '/'), '', $path));
        throw $e;
      } catch (\Error $e) {
        if (isset($path)) Storage::disk('public')->delete(str_ireplace(('/storage' . '/'), '', $path));
        throw $e;
      }
    }

    public function resetImages(ModelArticle $model): void
    {
      Gallery::where('model_id', $model->id)
        ->where('type', 'article')
        ->where('preview', 0)
        ->where('placement', 'text')
        ->update(['expires_at' => Carbon::now()])
        ;
      
      preg_match_all('/<img\s*src=\"(.*?)\".*?>/is', $model->text, $images);
      
      if (!empty($images[0])) {
        foreach($images[1] as $image) {
          $img = Gallery::query()
            ->where([
              'type' => 'article', 
              'model_id' => $model->id,
              'image' => $image,
            ])
            ->first();
          if ($img) {
            $img->update(['model_id' => $model->id, 'expires_at' => null]);
          }
        }
      }
    }

    public function resetTags(ModelArticle $model): void
    {
      ArticleTags::where('article_id', $model->id)->delete();
      if (!empty($this->tags)) {
        $input = is_array($this->tags) ? $this->tags : $this->tags->toArray();
        $tags = Tag::whereIn('slug', array_column($input, 'key'))->get();
        
        foreach ($this->tags as $tag) {
          $tag_model = $tags->where('slug', $tag['key'])->first();
          if (!$tag_model) {
            $tag_model = Tag::create(['title' => $tag['label']]);
          }
          if ($tag_model->status_id != Status::DELETED) {
            ArticleTags::create(['article_id' => $model->id, 'tag_id' => $tag_model->id]);
          }
        }
      }
    }

    public function render()
    {
      return view('livewire.forms.article');
    }
}
