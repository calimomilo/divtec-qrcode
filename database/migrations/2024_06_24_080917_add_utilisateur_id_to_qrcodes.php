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
        Schema::table('qrcodes', function (Blueprint $table) {
            $table->unsignedBigInteger('utilisateur_id')->nullable();
            $table->foreign('utilisateur_id')
                ->references('id')
                ->on('utilisateurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qrcodes', function (Blueprint $table) {
            //
        });
    }
};
