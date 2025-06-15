<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->integer('copies')->default(1);
            $table->integer('available_copies')->default(1);
        });

        // Set initial values for existing books
        DB::table('books')->update([
            'copies' => 1,
            'available_copies' => 1
        ]);
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['copies', 'available_copies']);
        });
    }
};
