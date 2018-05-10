<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('id')->primary();
            $table->string('transactionId');
            $table->date('fromDate');
            $table->date('toDate');
            $table->integer('merchant_id')->unsigned()->nullable();
            $table->foreign('merchant_id')->references('id')->on('users');
            $table->integer('acquirer_id')->unsigned()->nullable();
            $table->foreign('acquirer_id')->references('id')->on('acquirers');
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->integer('count');
            $table->integer('total');
            $table->string('currency');
            $table->timestamps();
            $table->string('status');
            $table->string('operation');
            $table->string('paymentMethod');
            $table->string('errorCode');
            $table->string('referenceNo');
            $table->string('message');
            $table->integer('agentInfoId');
            $table->boolean('isIpn');
            $table->boolean('isRefundable');
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
