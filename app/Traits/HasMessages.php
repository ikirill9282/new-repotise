<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Helpers\Collapse;

trait HasMessages
{

  protected int $messages_offset = 0;
  protected string $messages_type = 'child';

  public function getLimit()
  {
    return 2;
  }

  public function getMessages(int $offset = 0): Collection|array
  {
    $limit = $this->getLimit();
    $loaded = 0;

    $this->load(['messages' => function($query) use ($limit) {
      $query->with('likes.author.options', 'author.options')
        ->withCount('likes', 'messages')
        ->when(
          $this->messages_type == 'parent',
          fn($query) => $query->whereNull('parent_id')
        )
        ->limit($limit)
        ->offset($this->messages_offset)
        ;
    }]);

    // dd($this->toArray());
    $this->loadCount('messages');
    $loaded += $this->messages->count();


    foreach ($this->messages as $message) {
      if ($this->messages_type !== 'child') {
        $message->load(
          [
            'messages' => function($query) use($limit) {
              $query->with('likes.author.options', 'author.options')
                ->withCount('likes', 'messages')
                ->limit($limit);
            }, 
            'likes.author.options', 
            'author.options'
          ]
        );
        $message->loadCount('likes', 'messages');
      }
    }
    return $this->messages;
  }

  public function messagesCount(): Attribute
  {
    return Attribute::make(
      get: fn($value) => ($value == 0) ? 0 : Collapse::make($value),
    );
  }

  public function getUnloadedMessagesCount()
  {
    $arr = $this->toArray();
    if (!isset($arr['messages']) || empty($arr['messages'])) {
      return $this->messages_count;
    }

    if ($this->messages_type == 'parent') {
      return $this->messages()->whereNull('parent_id')->count() - $this->getLoadedMessagesCount() - $this->messages_offset;
    }
    return $this->messages_count - $this->getLoadedMessagesCount() - $this->messages_offset;
  }

  public function getLoadingMessagesCount()
  {
    $limit = $this->getLimit();
    $loaded = $this->getLoadedMessagesCount();
    $unloaded = $this->getUnloadedMessagesCount();
    $loaded += $limit;

    return $loaded > $unloaded ? $unloaded : $limit;
  }

  public function getLoadedMessagesCount()
  {
    $arr = $this->toArray();
    return (!isset($arr['messages']) || empty($arr['messages'])) ? 0 : count($arr['messages']);
  }

  public function messagesOffset(int $offset)
  {
    $this->messages_offset = $offset;
    return $this;
  }

  public function messagesType(string $type)
  {
    $this->messages_type = $type;
    return $this;
  }

  public function getMessagesOffset()
  {
    return $this->getLoadedMessagesCount() + $this->messages_offset;
  }

  public function getMessageType()
  {
    return $this->messages_type;
  }
}