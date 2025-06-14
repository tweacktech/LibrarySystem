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
        Schema::table('book_borrows', function (Blueprint $table) {
            $table->boolean('is_lost')->default(false);
            $table->decimal('lost_book_price', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_borrows', function (Blueprint $table) {
            $table->dropColumn(['is_lost', 'lost_book_price']);
        });
    }
};
