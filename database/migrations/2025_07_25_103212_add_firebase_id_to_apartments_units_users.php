<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->string('firebase_id')->nullable()->index();
        });
        Schema::table('units', function (Blueprint $table) {
            $table->string('firebase_id')->nullable()->index();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('firebase_id')->nullable()->index();
        });
    }
    
    public function down()
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn('firebase_id');
        });
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('firebase_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('firebase_id');
        });
    }
};
