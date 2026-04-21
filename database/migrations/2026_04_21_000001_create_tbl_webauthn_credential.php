<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_webauthn_credential', function (Blueprint $table) {
            $table->id('COD_WEBAUTHN_CREDENTIAL');
            $table->unsignedBigInteger('FK_COD_USUARIO')->index();
            $table->string('CREDENTIAL_ID', 512)->unique();
            $table->longText('PUBLIC_KEY_COSE');
            $table->unsignedBigInteger('SIGN_COUNT')->default(0);
            $table->string('NOMBRE')->nullable();
            $table->json('TRANSPORTS')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_webauthn_credential');
    }
};
