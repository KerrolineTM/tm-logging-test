<?php

namespace Tridmedia\Logging;

use Illuminate\Support\ServiceProvider;
use Tridmedia\Logging\Console\Commands\{
  LogMakeCommand,
  LoggerMakeCommand,
  LoggingSetupCommand,
};

class LoggingServiceProvider extends ServiceProvider
{
  public function boot()
  {


    $this->publishes([
      __DIR__ . '/config/config.php' => config_path('logging.php'),
    ]);


    if ($this->app->runningInConsole()) {
      $this->commands([
        LogMakeCommand::class,
        LoggerMakeCommand::class,
        LoggingSetupCommand::class,
      ]);
    }
  }

  public function register()
  {
    $this->app->register('Tridmedia\Logging\LoggingServiceProvider');

    $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'logging');
  }
}
