<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_task', function (Blueprint $table) {
            $table->foreignId('persons_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->primary(['task_id', 'persons_id']);
        
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_task');
    }
};