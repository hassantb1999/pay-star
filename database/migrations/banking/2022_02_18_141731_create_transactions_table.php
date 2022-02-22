<?php

use App\Modules\Banking\Models\Transaction;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->bigInteger('amount');
            $table->integer('transaction_type');
            $table->enum('type', [Transaction::TYPE_DEBIT, Transaction::TYPE_CREDIT]);
            $table->enum('status', [Transaction::STATUS_DONE, Transaction::STATUS_FAILED]);
            $table->timestamps();

          
            $table->index('account_id');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function(Blueprint $table) { 

            $table->dropForeign('transactions_account_id_foreign');
            $table->dropIndex('transactions_account_id_index');

        });

        Schema::dropIfExists('transactions');

       
    }
};
