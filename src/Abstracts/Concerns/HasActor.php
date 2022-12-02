<?php

namespace Tridmedia\Logging\Abstracts\Concerns;

use Illuminate\Database\Eloquent\Model;

use Tridmedia\Logging\Entities\Serializer;
use Tridmedia\Logging\Legacy\TypeError;

/**
 * Позволяет добавлять ссылку на пользователя
 */
trait HasActor
{
  /**
   * Экземпляр модели актора
   *
   * @var null|Model
   */
  protected $actor = null;



  /**
   * Геттер/Сеттер актора
   *
   * @param null|Model $actor Экземпляр модели актора
   * 
   * @return null|Model|static
   */
  public function actor($actor = null)
  {
    if (!count(func_get_args())) return $this->actor;

    if (!(is_null($actor) || $actor instanceof Model))
      throw TypeError::ofArgument('actor', 'actor', 'null|Model', $actor, 1);

    $this->actor = $actor;

    return $this;
  }



  /**
   * Возвращает наименование модели актора
   *
   * @return null|string
   */
  public function getActorType()
  {
    return $this->actor ? get_class($this->actor) : null;
  }

  /**
   * Возвращает идентификатор актора
   *
   * @return null|int|string
   */
  public function getActorIdentifier()
  {
    return $this->actor ? $this->actor->getKey() : null;
  }



  /**
   * Информация об акторе для серриализации
   * 
   * @return mixed
   */
  protected function actoragent()
  {
    return null;
  }

  /**
   * Серриализованная информация об акторе
   */
  protected function getSerializedActoragent(): ?array
  {
    return Serializer::make()
      ->implodeArrays()
      ->serializeAsFlatDictionary($this->actoragent());
  }
}
