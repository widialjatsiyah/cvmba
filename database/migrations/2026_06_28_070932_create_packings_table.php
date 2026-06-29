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
        Schema::create('packings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id'); // References the sale transaction
            $table->string('packing_name'); // Name of the Packing
            $table->date('packing_date'); // Date of Packing
            $table->unsignedBigInteger('created_by'); // User who created the Packing
            $table->unsignedBigInteger('business_id'); // Business identifier
            $table->timestamps();

            // Foreign key constraints
            // $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packings');
    }
};