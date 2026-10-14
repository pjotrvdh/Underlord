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
        Schema::create('waves', function (Blueprint $table): void {
            $table->unsignedInteger('number')->primary();
            $table->json('heroes'); // {"dwarf": 2, "archer": 1}
            // Game data comes from config/underlord; only changes made in the admin panel are tracked.
            $table->timestamp('updated_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
