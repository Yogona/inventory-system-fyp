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
        Schema::create('impared_instruments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("instrument_id");
            $table->string("fault");
            $table->text("description");
            $table->unsignedBigInteger("responsible_user");
            $table->unsignedBigInteger("status_id");
            $table->timestamps();

            $table->foreign("instrument_id")->references("id")->on("instruments")
            ->onDelete("restrict")->onUpdate("restrict");
            $table->foreign("responsible_user")->references("id")->on("users")->onDelete("restrict")
            ->onUpdate("restrict");
            $table->foreign("status_id")->references("id")->on("statuses")->onDelete("restrict")
            ->onUpdate("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impared_instruments');
    }
};
