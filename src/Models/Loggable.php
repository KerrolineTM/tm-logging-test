<?php

namespace Tridmedia\Logging\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

abstract class Loggable extends MorphPivot
{
  protected $table = 'loggables';

  public $timestamps = false;

  /**
   * Модель, которая привязана к этому пивоту
   */
  public function loggable(): MorphTo
  {
    return $this->morphTo();
  }
}
