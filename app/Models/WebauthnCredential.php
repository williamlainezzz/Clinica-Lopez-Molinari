<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebauthnCredential extends Model
{
    protected $table = 'tbl_webauthn_credential';
    protected $primaryKey = 'COD_WEBAUTHN_CREDENTIAL';

    protected $fillable = [
        'FK_COD_USUARIO',
        'CREDENTIAL_ID',
        'PUBLIC_KEY_COSE',
        'SIGN_COUNT',
        'NOMBRE',
        'TRANSPORTS',
    ];

    protected $casts = [
        'TRANSPORTS' => 'array',
        'SIGN_COUNT' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'FK_COD_USUARIO', 'COD_USUARIO');
    }
}
