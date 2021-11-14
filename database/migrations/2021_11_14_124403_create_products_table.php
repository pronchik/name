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
            $table->string('name');
            $table->text('price');

            $table->unsignedBigInteger('category_id')->nullable();
            $table->index('category_id', 'product_category_idx');
            $table->foreign('category_id', 'product_category_fk')->on('categories')->references('id');

            $table->unsignedBigInteger('seller_user_id')->nullable();
            $table->index('seller_user_id', 'product_seller_user_idx');
            $table->foreign('seller_user_id', 'product_seller_user_fk')->on('users')->references('id');

            $table->unsignedBigInteger('buyer_user_id')->nullable();
            $table->index('buyer_user_id', 'product_buyer_user_idx');
            $table->foreign('buyer_user_id', 'product_buyer_user_fk')->on('users')->references('id');

            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->index('owner_user_id', 'owner_buyer_user_idx');
            $table->foreign('owner_user_id', 'owner_user_fk')->on('users')->references('id');

            $table->unsignedBigInteger('status_id')->nullable();
            $table->index('status_id', 'status_idx');
            $table->foreign('status_id', 'status_fk')->on('statuses')->references('id');

            $table->timestamps();
            $table->softDeletes();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
