<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn('category'); // Remove the old category column
        $table->unsignedBigInteger('category_id')->after('description'); // Add category_id
    });
}

public function down()
{
    Schema::table('companies', function (Blueprint $table) {
        $table->string('category')->after('description'); // Add the old category column back if rolling back
        $table->dropColumn('category_id'); // Remove the new category_id column
    });
}

};
