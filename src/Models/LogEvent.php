<?php

namespace Tridmedia\Logging\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id            Id
 * @property string $title         Наименование
 * @property bool   $is_reportable Нужно ли оповещать о создании лога для данного события
 */
abstract class LogEvent extends Model
{
  // 
}
