<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccNumFieldToInvoiceDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('invoice_details', function(Blueprint $table) {
            $table->string('acc_bank')->nullable();
            $table->string('acc_num')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_details', function(Blueprint $table) {
            $table->dropColumn('acc_bank');
            $table->dropColumn('acc_num');
        });
    }
}
