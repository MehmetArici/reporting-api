<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('deleted_at');
            $table->timestamps();
            $table->string('number');
            $table->string('expiryMonth');
            $table->string('expiryYear');
            $table->string('startMonth');
            $table->string('startYear');
            $table->string('issueNumber');
            $table->string('email');
            $table->dateTime('birthday');
            $table->string('gender');
            $table->string('billingTitle');
            $table->string('billingFirstName');
            $table->string('billingLastName');
            $table->string('billingCompany');
            $table->string('billingAddress1');
            $table->string('billingAddress12');
            $table->string('billingCity');
            $table->string('billingPostCode');
            $table->string('billingState');
            $table->string('billingCountry');
            $table->string('billingPhone');
            $table->string('billingFax');
            $table->string('shippingTitle');
            $table->string('shippingFirstName');
            $table->string('shippingLastName');
            $table->string('shippingCompany');
            $table->string('shippingAddress1');
            $table->string('shippingAddress2');
            $table->string('shippingCity');
            $table->string('shippingPostCode');
            $table->string('shippingState');
            $table->string('shippingCountry');
            $table->string('shippingPhone');
            $table->string('shippingFax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
