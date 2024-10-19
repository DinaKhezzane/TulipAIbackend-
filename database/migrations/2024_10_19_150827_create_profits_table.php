<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfitsTable extends Migration
{
    public function up()
    {
        Schema::create('profits', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('revenue', 10, 2);
            $table->decimal('cogs', 10, 2);
            $table->decimal('operating_expenses', 10, 2);
            $table->decimal('gross_profit', 10, 2);
            $table->decimal('net_profit', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profits');
    }
}
