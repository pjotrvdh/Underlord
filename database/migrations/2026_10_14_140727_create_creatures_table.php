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
        Schema::create('creatures', function (Blueprint $table): void {
            $table->string('key')->primary(); // the handle used in config/underlord and by the game
            $table->string('side'); // 'creature' (yours) or 'hero'
            $table->string('name');
            $table->unsignedInteger('hp');
            $table->unsignedInteger('attack');
            $table->decimal('speed', 5, 2);
            $table->decimal('range', 5, 2)->default(1);
            $table->decimal('rate', 5, 2)->default(1);
            $table->unsignedInteger('wage')->default(0);
            $table->unsignedInteger('vision')->default(5);
            $table->unsignedInteger('pool')->default(0);
            $table->json('attract')->nullable(); // minimum room sizes, e.g. {"library": 9}
            $table->unsignedInteger('bounty')->default(0);
            $table->decimal('dig', 5, 2)->nullable();
            $table->boolean('big')->default(false);
            // Game data comes from config/underlord; only changes made in the admin panel are tracked.
            $table->timestamp('updated_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
