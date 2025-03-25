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
            $table->string('cpf')->nullable(); // roles: admin, assessor, citizen
            $table->string('phone')->nullable(); // roles: admin, assessor, citizen
            $table->string('instagram')->nullable(); // roles: admin, assessor, citizen
            $table->string('facebook')->nullable(); // roles: admin, assessor, citizen

            $table->string('zip')->nullable(); // roles: admin, assessor, citizen
            $table->string('location')->nullable(); // roles: admin, assessor, citizen
            $table->string('latitude')->nullable(); // roles: admin, assessor, citizen
            $table->string('longitude')->nullable(); // roles: admin, assessor, citizen

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cpf');
            $table->dropColumn('phone');
            $table->dropColumn('instagram');
            $table->dropColumn('facebook');

            $table->dropColumn('zip');
            $table->dropColumn('location');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');


        });
    }
};
