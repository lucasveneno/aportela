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
        Schema::table('demands', function (Blueprint $table) {
            $table->string('city')->nullable(); // roles: admin, assessor, citizen
            $table->string('zip')->nullable(); // roles: admin, assessor, citizen
            $table->string('address')->nullable(); // roles: admin, assessor, citizen
            $table->string('latitude')->nullable(); // roles: admin, assessor, citizen
            $table->string('longitude')->nullable(); // roles: admin, assessor, citizen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('zip');
            $table->dropColumn('address');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
};
