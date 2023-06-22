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
        Schema::create('instruments_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("requester");
            $table->unsignedBigInteger("instrument_id");
            $table->unsignedInteger("quantity");
            $table->unsignedBigInteger("allocatee");
            $table->unsignedBigInteger("allocatee_sign")->nullable();
            $table->unsignedBigInteger("status_id")->default(1);
            $table->unsignedBigInteger("store_id");
            $table->unsignedBigInteger("assignment_id");
            $table->unsignedInteger("days");
            $table->dateTime("deadline");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("requester")->references("id")->on("users")->onDelete("cascade")
            ->onUpdate("restrict");
            // $table->foreign("instrument_id")->references("id")->on("instruments")
            // ->onDelete("cascade")->onUpdate("restrict");
            // $table->foreign("allocatee")->references("id")->on("users")->onDelete("cascade")
            // ->onUpdate("restrict");
            // $table->foreign("status_id")->references("id")->on("statuses")->onDelete("restrict")
            // ->onUpdate("restrict");
            // $table->foreign("store_id")->references("id")->on("stores")->onDelete("cascade")
            // ->onUpdate("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instruments_requests');
    }
};
