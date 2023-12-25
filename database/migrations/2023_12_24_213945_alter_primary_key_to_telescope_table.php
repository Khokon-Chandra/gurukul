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
        Schema::table('telescope_entries_tags', function (Blueprint $table) {
            $table->primary(['entry_uuid','tag'],'add_primary_key_entry_uuid_and_tag_column');
        });

        Schema::table('telescope_monitoring', function (Blueprint $table) {
            $table->primary('tag','add_primary_key_to_tag_column');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
