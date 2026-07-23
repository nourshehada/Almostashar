<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('options', function (Blueprint $table) {

            $table->integer('analysis')->default(0);
            $table->integer('creativity')->default(0);
            $table->integer('leadership')->default(0);
            $table->integer('communication')->default(0);
            $table->integer('research')->default(0);
            $table->integer('business')->default(0);
            $table->integer('technology')->default(0);
            $table->integer('humanitarian')->default(0);
            $table->integer('scientific')->default(0);
            $table->integer('adaptability')->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {

            $table->dropColumn([
                'analysis',
                'creativity',
                'leadership',
                'communication',
                'research',
                'business',
                'technology',
                'humanitarian',
                'scientific',
                'adaptability'
            ]);

        });
    }
};
