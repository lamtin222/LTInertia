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
        Schema::create('data_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_type_id')->constrained()->onDelete('cascade');
            $table->string('field'); // Tên cột (e.g., title)
            $table->string('type'); // Loại input (e.g., text, textarea)
            $table->string('display_name'); // Tên hiển thị (e.g., Title)
            $table->boolean('required')->default(false);
            $table->boolean('browse')->default(true); // Hiển thị trong Browse
            $table->boolean('read')->default(true); // Hiển thị trong Read
            $table->boolean('edit')->default(true); // Hiển thị trong Edit
            $table->boolean('add')->default(true); // Hiển thị trong Add
            $table->boolean('delete')->default(true); // Hiển thị trong Delete
            $table->text('details')->nullable(); // JSON tùy chọn (e.g., rules)
            $table->integer('order')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_rows');
    }
};
