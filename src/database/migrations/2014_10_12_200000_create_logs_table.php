<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('logs', function (Blueprint $table) {
      $table->increments('id');
      $table->json('content')->nullable();
      $table->json('details')->nullable();
      $table->json('actoragent')->nullable();
      $table->string('type')->nullable();
      $table->nullableMorphs('actor');
      $table->unsignedBigInteger('log_event_id')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('logs');
  }
}
