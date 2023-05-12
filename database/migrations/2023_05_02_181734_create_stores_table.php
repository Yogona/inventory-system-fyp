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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("location");
            $table->unsignedBigInteger("store_keeper");
            $table->unsignedBigInteger("department_id");
            $table->timestamps();

            $table->foreign("store_keeper")->references("id")->on("users")->onDelete(null)
            ->onUpdate("restrict");
            $table->foreign("department_id")->references("id")->on("departments")->onDelete(null)
            ->onUpdate("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
