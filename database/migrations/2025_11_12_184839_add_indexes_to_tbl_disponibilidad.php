<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tbl_disponibilidad', function (Blueprint $table) {
            $table->index(['FEC_DISPONIBILIDAD', 'FK_COD_DOCTOR'], 'idx_disp_fecha');
        });
    }
    public function down(): void {
        Schema::table('tbl_disponibilidad', function (Blueprint $table) {
            $table->dropIndex('idx_disp_fecha');
        });
    }
};
