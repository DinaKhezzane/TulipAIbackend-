<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Financial Ratios Table
        Schema::create('financial_ratios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->date('date');
            $table->decimal('current_assets', 15, 2);
            $table->decimal('inventory', 15, 2);
            $table->decimal('current_liabilities', 15, 2);
            $table->decimal('total_liabilities', 15, 2);
            $table->decimal('equity', 15, 2);
            $table->timestamps();
        });

        Schema::create('inflow_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        Schema::create('outflow_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        Schema::create('inflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('inflow_category_id')->constrained('inflow_categories')->onDelete('cascade'); // Foreign key to inflow categories
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        Schema::create('outflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('outflow_category_id')->constrained('outflow_categories')->onDelete('cascade'); // Foreign key to outflow categories
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
        

        // Top Products Table
        Schema::create('top_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('product_id')->unique();
            $table->string('product_name');
            $table->decimal('revenue', 15, 2);
            $table->integer('units_sold');
            $table->decimal('profit_margin', 5, 2);
            $table->timestamps();
        });

        // Top Clients Table
        Schema::create('top_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('client_id')->unique();
            $table->string('client_name');
            $table->decimal('revenue', 15, 2);
            $table->integer('volume');
            $table->date('last_transaction_date');
            $table->timestamps();
        });

        // Profit Evolution Table
        Schema::create('profit_evolution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->date('date');
            $table->decimal('revenue', 15, 2);
            $table->decimal('cost_of_goods_sold', 15, 2);
            $table->decimal('operating_expenses', 15, 2);
            $table->decimal('gross_profit', 15, 2);
            $table->decimal('net_profit', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profit_evolution');
        Schema::dropIfExists('top_clients');
        Schema::dropIfExists('top_products');
        Schema::dropIfExists('inflows');
        Schema::dropIfExists('outflows');
        Schema::dropIfExists('financial_ratios');
    }
}
