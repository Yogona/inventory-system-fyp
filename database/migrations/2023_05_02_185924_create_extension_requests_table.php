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
        Schema::create('extension_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("assignment");
            $table->unsignedBigInteger("store_id");
            $table->unsignedBigInteger("requester");
            $table->unsignedInteger("extra_days");
            $table->timestamps();

            $table->foreign("assignment")->references("id")->on("instruments_requests")
            ->onDelete("cascade")->onUpdate("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extension_requests');
    }
};
