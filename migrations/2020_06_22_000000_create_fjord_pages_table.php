<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFjordPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fjord_pages', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('collection');
            $table->string('config_type');
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('value')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();
        });

        Schema::create('fjord_page_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fjord_page_id')->unsigned();
            $table->string('locale')->index();

            $table->string('t_title')->nullable();
            $table->string('t_slug')->nullable();
            $table->text('value')->nullable();

            $table->unique(['fjord_page_id', 'locale']);
            $table->foreign('fjord_page_id')->references('id')->on('fjord_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fjord_page_translations');
        Schema::dropIfExists('fjord_pages');
    }
}
