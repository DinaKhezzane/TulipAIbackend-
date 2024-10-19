<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDashboardMetricsTable extends Migration
{
    public function up()
    {
        Schema::create('dashboard_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->decimal('working_capital', 15, 2)->default(0);
            $table->decimal('leverage', 5, 2)->default(0);
            $table->decimal('quick_ratio', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dashboard_metrics');
    }
}
