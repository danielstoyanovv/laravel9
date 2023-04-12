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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
            $table->string('payment_data')->nullable()->change();
            $table->float('refund_amount')->nullable()->change();
            $table->string('invoice_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->change();
            $table->string('payment_data')->change();
            $table->float('refund_amount')->change();
            $table->string('invoice_number')->change();
        });
    }
};
