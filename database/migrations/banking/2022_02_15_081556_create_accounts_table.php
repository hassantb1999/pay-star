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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('bank');
            $table->bigInteger('credit')->default(0);
            $table->string('description')->nullable();
            $table->string('account_number');
            $table->string('shaba_number');
            $table->softDeletes();
            $table->timestamps();

            $table->index('owner_id');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function(Blueprint $table) {
            $table->dropForeign('accounts_user_id_foreign');
            $table->dropIndex('accounts_user_id_index');
        });

        Schema::dropIfExists('accounts');
    }
};
