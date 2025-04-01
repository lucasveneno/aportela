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
            $table->string('demand_code')->nullable(); // roles: admin, assessor, citizen
            $table->string('category_id')->nullable(); // roles: admin, assessor, citizen
            $table->string('applicant_demand_origin')->nullable(); // roles: admin, assessor, citizen
            $table->json('applicants')->nullable(); // roles: admin, assessor, citizen
            $table->string('prioritization_criteria')->nullable(); // roles: admin, assessor, citizen
            $table->string('priority')->nullable(); // roles: admin, assessor, citizen

            $table->string('full_address')->nullable(); // roles: admin, assessor, citizen
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
            $table->dropColumn('full_address');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
};
