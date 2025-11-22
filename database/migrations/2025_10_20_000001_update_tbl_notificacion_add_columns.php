<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_notificacion')) {
            return;
        }

        Schema::table('tbl_notificacion', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_notificacion', 'TIPO_NOTIFICACION')) {
                $table->enum('TIPO_NOTIFICACION', ['CREACION', 'RECORDATORIO_24H', 'RECORDATORIO_1H', 'MANUAL'])
                    ->default('CREACION');
            }

            if (!Schema::hasColumn('tbl_notificacion', 'LEIDA')) {
                $table->boolean('LEIDA')->default(false);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_notificacion')) {
            return;
        }

        Schema::table('tbl_notificacion', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_notificacion', 'LEIDA')) {
                $table->dropColumn('LEIDA');
            }

            if (Schema::hasColumn('tbl_notificacion', 'TIPO_NOTIFICACION')) {
                $table->dropColumn('TIPO_NOTIFICACION');
            }
        });
    }
};
