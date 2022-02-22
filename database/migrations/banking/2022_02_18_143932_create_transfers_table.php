<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->string('track_id');
            $table->string('description');
            $table->string('destinationFirstname');
            $table->string('destinationLastname');
            $table->string('destinationNumber');
            $table->string('inquiryDate')->nullable();
            $table->string('inquiryTime')->nullable();
            $table->string('inquirySequence')->nullable();
            $table->string('message')->nullable();
            $table->string('paymentNumber');
            $table->string('refCode');
            $table->string('type');

            $table->index('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function(Blueprint $table) {

            $table->dropForeign('transfers_transaction_id_foreign');
            $table->dropIndex('transfers_transaction_id_index');

        });

        Schema::dropIfExists('transfers');

    }
};
