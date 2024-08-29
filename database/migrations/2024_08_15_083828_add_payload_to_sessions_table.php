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
        Schema::table('sessions', function (Blueprint $table) {
            // Check if the 'payload' column does not exist before adding it
            if (!Schema::hasColumn('sessions', 'payload')) {
                $table->longText('payload')->after('user_agent')->notNull();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            if (Schema::hasColumn('sessions', 'payload')) {
                $table->dropColumn('payload');
            }
        });
    }
};
