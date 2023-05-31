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
        Schema::table('users', function ($table) {
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
        if (Schema::hasColumn('users', 'picture'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('picture');
            });
        }
        if (Schema::hasColumn('users', 'code'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('code');
            });
        }
        if (Schema::hasColumn('users', 'fingerprint'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('fingerprint');
            });
        }
        if (Schema::hasColumn('users', 'language'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('language');
            });
        }
        if (Schema::hasColumn('users', 'profile'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('profile');
            });
        }
        if (Schema::hasColumn('users', 'isAdmin'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('isAdmin');
            });
        }
        if (Schema::hasColumn('users', 'isWebEnabled'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('isWebEnabled');
            });
        }
        if (Schema::hasColumn('users', 'isEntryEnabled'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('isEntryEnabled');
            });
        }
        if (Schema::hasColumn('users', 'isEmployee'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('isEmployee');
            });
        }
    }
};
