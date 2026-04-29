<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dashboards', function (Blueprint $table) {
            $table->unsignedBigInteger('dataset_id')->nullable()->after('user_id');
        });

        // Backfill older dashboards using the first linked visualization dataset
        DB::statement('
            UPDATE dashboards
            SET dataset_id = (
                SELECT visualizations.dataset_id
                FROM visualizations
                WHERE visualizations.dashboard_id = dashboards.id
                ORDER BY visualizations.id ASC
                LIMIT 1
            )
            WHERE dataset_id IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dashboards', function (Blueprint $table) {
            $table->dropColumn('dataset_id');
        });
    }
};
