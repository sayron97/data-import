<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->unsigned()->index();
            $table->bigInteger('manufacturer_id')->unsigned()->index();
            $table->string('name');
            $table->string('code');
            $table->text('description');
            $table->integer('amount');
            $table->integer('garancy')->nullable();
            $table->boolean('in_stock')->default(1);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id', 'manufacturer_id']);
        });
        Schema::dropIfExists('products');
    }
}
