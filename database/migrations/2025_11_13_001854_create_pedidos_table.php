<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('aberto');    // aberto | finalizado | cancelado

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pedidos');
    }
};
