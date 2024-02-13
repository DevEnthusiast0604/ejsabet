<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('description')->nullable();
            $table->integer('from')->nullable();
            $table->integer('to')->nullable();
            $table->integer('status')->nullable();
            $table->integer('amount')->nullable();
            $table->dateTime('timestamp')->useCurrent();
            $table->string('attachment')->nullable();
            $table->boolean('is_audited')->default(0)->nullable();
            $table->boolean('is_approved_by_super_admin')->default(0)->nullable();
            $table->unsignedBigInteger('authorized_by_id')->nullable();
            $table->boolean('is_uploading')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('transactions');
    }
}
