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
        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['TODO', 'IN_PROGRESS', 'READY_FOR_TEST', 'PO_REVIEW', 'DONE', 'REJECTED'])->default('TODO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};
