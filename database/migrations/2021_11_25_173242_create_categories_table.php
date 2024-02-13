<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('name');
            $table->text('comment')->nullable();
            $table->integer('from_account_id')->nullable();
            $table->integer('to_account_id')->nullable();
            $table->string('type')->default('expense');
            $table->string('status')->default('active');
            $table->boolean('must_be_approved_by_super_admin')->default(0);
            $table->timestamp('must_be_approved_from')->nullable();
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
        Schema::dropIfExists('categories');
    }
}
