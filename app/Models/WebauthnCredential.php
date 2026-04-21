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

    public function getDisplayNameAttribute(): string
    {
        return self::friendlyDeviceName($this->NOMBRE ?: '');
    }

    public static function friendlyDeviceName(string $userAgent): string
    {
        $userAgent = trim($userAgent);

        if ($userAgent === '') {
            return 'Dispositivo biometrico';
        }

        $device = self::detectDevice($userAgent);
        $browser = self::detectBrowser($userAgent);

        return trim($device . ($browser !== '' ? ' - ' . $browser : ''));
    }

    private static function detectDevice(string $userAgent): string
    {
        if (stripos($userAgent, 'iPhone') !== false) {
            return 'iPhone';
        }

        if (stripos($userAgent, 'iPad') !== false) {
            return 'iPad';
        }

        if (stripos($userAgent, 'Android') !== false) {
            $model = self::androidModel($userAgent);
            return $model !== '' ? 'Android ' . $model : 'Telefono Android';
        }

        if (stripos($userAgent, 'Windows') !== false) {
            return 'PC Windows';
        }

        if (stripos($userAgent, 'Macintosh') !== false || stripos($userAgent, 'Mac OS X') !== false) {
            return 'Mac';
        }

        if (stripos($userAgent, 'Linux') !== false) {
            return 'PC Linux';
        }

        return 'Dispositivo biometrico';
    }

    private static function detectBrowser(string $userAgent): string
    {
        if (preg_match('/Edg\/([\d.]+)/', $userAgent)) {
            return 'Microsoft Edge';
        }

        if (preg_match('/OPR\/([\d.]+)/', $userAgent)) {
            return 'Opera';
        }

        if (preg_match('/CriOS\/([\d.]+)/', $userAgent) || preg_match('/Chrome\/([\d.]+)/', $userAgent)) {
            return 'Chrome';
        }

        if (preg_match('/Firefox\/([\d.]+)/', $userAgent)) {
            return 'Firefox';
        }

        if (preg_match('/Version\/([\d.]+).*Safari\//', $userAgent)) {
            return 'Safari';
        }

        return '';
    }

    private static function androidModel(string $userAgent): string
    {
        if (!preg_match('/Android [^;)]*;\s*([^;)]+)\)/', $userAgent, $matches)) {
            return '';
        }

        $model = trim($matches[1]);
        $model = preg_replace('/\s+Build\/.*$/i', '', $model) ?: $model;
        $model = preg_replace('/\s+wv$/i', '', $model) ?: $model;

        if ($model === '' || stripos($model, 'Mobile') !== false) {
            return '';
        }

        return $model;
    }
}
