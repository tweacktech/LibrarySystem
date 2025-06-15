<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table): void {
                $table->id();
                $table->string('group');
                $table->string('name');
                $table->boolean('locked')->default(false);
                $table->json('payload');
                $table->timestamps();
                $table->unique(['group', 'name']);
            });
        } else {
            // Schema::table('settings', function (Blueprint $table) {
            //     // Check and add missing columns if needed
            //     if (!Schema::hasColumn('settings', 'group')) {
            //         $table->string('group')->after('id');
            //     }
            //     if (!Schema::hasColumn('settings', 'name')) {
            //         $table->string('name')->after('group');
            //     }
            //     if (!Schema::hasColumn('settings', 'locked')) {
            //         $table->boolean('locked')->default(false)->after('name');
            //     }
            //     if (!Schema::hasColumn('settings', 'payload')) {
            //         $table->json('payload')->after('locked');
            //     }

            //     // Add composite unique index if it doesn't exist
            //     $sm = Schema::getConnection()->getDoctrineSchemaManager();
            //     $indexesFound = $sm->listTableIndexes('settings');
            //     if (!isset($indexesFound['settings_group_name_unique'])) {
            //         $table->unique(['group', 'name']);
            //     }
            // });
        }
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};