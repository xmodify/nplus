<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productivity_hd', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->string('shift_time', 20);
            $table->integer('nurse_fulltime')->nullable();
            $table->integer('nurse_partime')->nullable();
            $table->integer('nurse_oncall')->nullable();
            $table->string('recorder')->nullable();
            $table->string('note')->nullable();
            $table->integer('patient_all')->nullable();
            $table->double('nursing_hours', 5, 2)->nullable();
            $table->double('working_hours', 5, 2)->nullable();
            $table->double('nhppd', 5, 2)->nullable();
            $table->double('nurse_shift_time', 5, 2)->nullable();
            $table->double('productivity', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productivity_hd');
    }
};
