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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id()->unsigned();
            $table->unsignedBigInteger('user_type_id');
            $table->string('name', 70);
            $table->unsignedBigInteger('document_type_id');
            $table->string('document_number', 14)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_type_id')
                  ->references('id')
                  ->on('user_types');

            $table->foreign('document_type_id')
                  ->references('id')
                  ->on('document_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
