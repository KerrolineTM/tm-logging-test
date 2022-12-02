<?php

namespace Tridmedia\Logging\Abstracts;

use Tridmedia\Logging\Models\Log as BaseLog;

abstract class AbstractLogConstructor
{
  use Concerns\HasActor;
  use Concerns\HasDetails;
  use Concerns\HasReferences;

  /**
   * Тип лога
   *
   * @var null|string
   */
  protected $type = null;

  /**
   * Уникальный идентификатор события по которому конструируется лог
   *
   * @var null|int
   */
  protected $event = null;



  /**
   * Создает экземпляр конструктора лога
   *
   * @return static
   */
  public static function make()
  {
    return new static;
  }



  /** 
   * Сохраняет сконструированный лог 
   *
   * @return BaseLog
   */
  abstract public function save();
}
