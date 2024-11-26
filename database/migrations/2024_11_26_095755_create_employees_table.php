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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('major_id')->index()->nullable();
            $table->string('NIP', 50)->nullable();
            $table->string('nm_employee', 200);
            $table->string('gender', 10);
            $table->string('phone', 20);
            $table->date('hire_date')->nullable();
            $table->string('img_employee')->nullable();
            $table->date('birthdate');
            $table->text('address');
            $table->string('jabatan'); // dosen, staf, satpam, pekarya
            $table->string('status')->default('aktif'); // aktif, pindah, pensiun
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
