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
            $table->string('picture');
            $table->string('code');
            $table->string('fingerprint');
            $table->string('language');
            $table->string('profile');
            $table->boolean('isAdmin');
            $table->boolean('isWebEnabled');
            $table->boolean('isEntryEnabled');
            $table->boolean('isEmployee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
