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
        Schema::create('transfers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id()->unsigned();
            $table->unsignedBigInteger('payer_id');
            $table->unsignedBigInteger('payee_id');
            $table->decimal('amount', 10, 2)->default(0);
            $table->unsignedBigInteger('transfer_status_id');
            $table->timestamps();

            $table->foreign('payer_id')
                  ->references('id')
                  ->on('users');

            $table->foreign('payee_id')
                  ->references('id')
                  ->on('users');

            $table->foreign('transfer_status_id')
                  ->references('id')
                  ->on('transfer_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
