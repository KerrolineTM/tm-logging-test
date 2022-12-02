<?php

namespace Tridmedia\Logging\Abstracts;

abstract class AbstractLogger
{
  /**
   * Инициализирует конструктор лога, для внесения данных по умолчанию
   *
   * @param AbstractLogConstructor $constructor Конструктор лога
   */
  protected static function initializeConstructor(AbstractLogConstructor $constructor): AbstractLogConstructor
  {
    return $constructor;
  }
}
