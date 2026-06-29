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
        Schema::create('detail_packings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('packing_id'); // References the packing record
            $table->unsignedBigInteger('transaction_sell_line_id'); // References the sell line item
            $table->decimal('quantity_packed', 8, 2); // Quantity packed for this item
            $table->text('notes')->nullable(); // Additional notes for this packing detail
            $table->timestamps();

            // Foreign key constraints
            // $table->foreign('packing_id')->references('id')->on('packings')->onDelete('cascade');
            // $table->foreign('transaction_sell_line_id')->references('id')->on('transaction_sell_lines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_packings');
    }
};