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
        Schema::create('tasks',function(Blueprint $table){
            $table->id();
            $table->string("hash")->unique()->nullable();
            $table->string("list_id");
            $table->string("taskName");
            $table->string("description");
            $table->boolean("status")->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("list_id")->references("hash")->on("lists")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');

        Schema::table("tasks",function(Blueprint $table){
            $table->dropForeign(["list_id"]);
        });
    }
};
