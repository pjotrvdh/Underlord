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
        Schema::create('spells', function (Blueprint $table): void {
            $table->string('key')->primary(); // the handle used in config/underlord and by the game
            $table->string('name');
            $table->unsignedInteger('research')->default(0);
            $table->unsignedInteger('cost')->default(0);
            $table->json('params')->nullable(); // spell specific: damage, radius, duration
            // Game data comes from config/underlord; only changes made in the admin panel are tracked.
            $table->timestamp('updated_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
