<?php

namespace Tridmedia\Logging;

use Illuminate\Support\ServiceProvider;
use Tridmedia\Logging\Console\Commands\{
  DataLogSetupCommand,
  LogMakeCommand,
  LoggerMakeCommand,
};

class LoggingServiceProvider extends ServiceProvider
{
  public function boot()
  {
    $this->app->register('Tridmedia\Logging\LoggingServiceProvider');

    $this->publishes([
      __DIR__ . '/config/config.php' => config_path('logging.php'),
    ]);


    if ($this->app->runningInConsole()) {
      $this->commands([
        LogMakeCommand::class,
        LoggerMakeCommand::class,
        DataLogSetupCommand::class,
      ]);
    }
  }

  public function register()
  {
    $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'logging');
  }
}
