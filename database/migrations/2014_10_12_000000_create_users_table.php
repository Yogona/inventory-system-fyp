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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("first_name", 255);
            $table->string("last_name", 255);
            $table->string('username')->unique();
            $table->char('gender',1);
            $table->string("email")->unique();
            $table->string("phone", 15)->unique();
            $table->string('password');
            $table->unsignedBigInteger("role_id")->default(5);
            // $table->unsignedBigInteger("department_id")->nullable()->default(NULL);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign("role_id")->references("id")->on("roles")->onDelete(null)
            ->onUpdate("restrict");
            // $table->foreign("department_id")->references("id")->on("departments")->onDelete(null)
            // ->onUpdate("restrict");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
