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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('isAdmin');
            $table->dropColumn('isWebEnabled');
            $table->dropColumn('isEmployee');
            $table->string('role')->nullable(); //Ez lesz az új jogosultságot jelző oszlop
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean("isAdmin");
            $table->boolean("isWebEnabled");
            $table->boolean("isEmployee");
        });
    }
};
