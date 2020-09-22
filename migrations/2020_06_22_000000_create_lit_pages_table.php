<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLitPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lit_pages', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('collection');
            $table->string('config_type');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('value')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();
        });

        Schema::create('lit_page_translations', function (Blueprint $table) {
            $table->translates('lit_pages');

            $table->string('t_title')->nullable();
            $table->string('t_slug')->nullable();
            $table->text('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lit_page_translations');
        Schema::dropIfExists('lit_pages');
    }
}
