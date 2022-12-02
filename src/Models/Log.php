<?php

namespace Tridmedia\Logging\Models;

use Illuminate\Database\Eloquent\Model;

use Tridmedia\Logging\Relations\HasManyLoggables;

/**
 * @property int $id Id
 * @property null|string|array $content Данные лога
 * @property null|array<int|float|string> $details Подробности по логу
 * @property null|string $type Тип лога
 * @property null|int $log_event_id Id события лога
 * @property null|int $actor_id Id пользователя создавшего лог
 * 
 * @method HasManyLoggables loggables()
 */
abstract class Log extends Model
{
  use Concerns\HasManyLoggablesRelationship;



  protected $casts = [
    'content' => 'array',
    'details' => 'array',
    'actoragent' => 'array',
  ];



  /**
   * Список моделей, привязанных к этому логу с группировкой по типу
   * 
   * Чтобы избежать n+1 модель нужно подгрузить с отношением 'loggables.loggable'
   */
  public function getRelatedModelsGroups()
  {
    return $this->loggables->groupBy('loggable_type')->map->pluck('loggable');
  }

  /**
   * Список моделей, привязанных к этому логу
   * 
   * Чтобы избежать n+1 модель нужно подгрузить с отношением 'loggables.loggable'
   */
  public function getRelatedModels()
  {
    return $this->loggables->pluck('loggable');
  }
}
