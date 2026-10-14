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
        Schema::create('saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slot')->default('slot1');
            $table->unsignedInteger('version');
            $table->unsignedInteger('wave')->default(0);
            $table->longText('data');
            $table->timestamps();
            $table->unique(['user_id', 'slot']);
        });
    }
};
