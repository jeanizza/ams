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
        if (!Schema::hasTable('job_requests')) {
            Schema::create('job_requests', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['Simple', 'Complex']);
                $table->dateTime('date_time_requested');
                $table->string('name');
                $table->string('division');
                $table->set('type_of_request', ['Repair', 'Maintenance', 'Others']);
                $table->string('specify')->nullable();
                $table->text('job_description');
                $table->timestamps(); // Adds created_at and updated_at columns
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
