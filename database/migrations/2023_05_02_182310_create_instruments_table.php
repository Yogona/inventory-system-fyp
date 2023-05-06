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
        Schema::create('instruments', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description");
            $table->unsignedInteger("quantity");
            $table->string("code");
            $table->unsignedBigInteger("added_by");
            $table->unsignedBigInteger("store_id");
            $table->timestamps();

            $table->foreign("added_by")->references("id")->on("users")->onDelete(null)
            ->onUpdate("restrict");
            $table->foreign("store_id")->references("id")->on("stores")->onDelete("cascade")
            ->onUpdate("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruments');
    }
};
