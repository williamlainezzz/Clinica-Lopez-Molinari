<?php

namespace App\Support\WebAuthn;

use App\Models\WebauthnCredential;
use Illuminate\Http\Request;

class WebAuthnService
{
    public function rpId(Request $request): string
    {
        return parse_url($request->getSchemeAndHttpHost(), PHP_URL_HOST) ?: $request->getHost();
    }

    public function expectedOrigin(Request $request): string
    {
        return $request->getSchemeAndHttpHost();
    }

    public function verifyRegistration(Request $request, array $payload, string $expectedChallenge): array
    {
        $clientDataJson = Base64Url::decode($payload['response']['clientDataJSON'] ?? '');
        $clientData = json_decode($clientDataJson, true);

        $this->assertClientData($request, $clientData, 'webauthn.create', $expectedChallenge);

        $attestation = (new CborDecoder())->decode(Base64Url::decode($payload['response']['attestationObject'] ?? ''));
        $authData = $attestation['authData'] ?? null;

        if (!is_string($authData)) {
            throw new \InvalidArgumentException('La respuesta biometrica no contiene datos de autenticador.');
        }

        $parsed = $this->parseAuthenticatorData($request, $authData, requireAttestedCredential: true);

        return [
            'credential_id' => Base64Url::encode($parsed['credential_id']),
            'public_key_cose' => Base64Url::encode($parsed['public_key_cose']),
            'sign_count' => $parsed['sign_count'],
        ];
    }

    public function verifyAuthentication(Request $request, WebauthnCredential $credential, array $payload, string $expectedChallenge): int
    {
        $clientDataJson = Base64Url::decode($payload['response']['clientDataJSON'] ?? '');
        $clientData = json_decode($clientDataJson, true);

        $this->assertClientData($request, $clientData, 'webauthn.get', $expectedChallenge);

        $authenticatorData = Base64Url::decode($payload['response']['authenticatorData'] ?? '');
        $parsed = $this->parseAuthenticatorData($request, $authenticatorData, requireAttestedCredential: false);

        $signature = Base64Url::decode($payload['response']['signature'] ?? '');
        $signedData = $authenticatorData . hash('sha256', $clientDataJson, true);
        $publicKey = $this->coseToPem(Base64Url::decode($credential->PUBLIC_KEY_COSE));

        if (openssl_verify($signedData, $signature, $publicKey, OPENSSL_ALGO_SHA256) !== 1) {
            throw new \InvalidArgumentException('No se pudo validar la firma biometrica.');
        }

        $previousCount = (int) $credential->SIGN_COUNT;
        $newCount = (int) $parsed['sign_count'];

        if ($previousCount > 0 && $newCount > 0 && $newCount <= $previousCount) {
            throw new \InvalidArgumentException('El contador del autenticador no es valido.');
        }

        return $newCount;
    }

    private function assertClientData(Request $request, ?array $clientData, string $type, string $expectedChallenge): void
    {
        if (!is_array($clientData)) {
            throw new \InvalidArgumentException('Respuesta biometrica invalida.');
        }

        if (($clientData['type'] ?? null) !== $type) {
            throw new \InvalidArgumentException('Tipo de respuesta biometrica inesperado.');
        }

        if (($clientData['challenge'] ?? null) !== $expectedChallenge) {
            throw new \InvalidArgumentException('El reto biometrico expiro o no coincide.');
        }

        if (($clientData['origin'] ?? null) !== $this->expectedOrigin($request)) {
            throw new \InvalidArgumentException('Origen biometrico invalido.');
        }

        if (($clientData['crossOrigin'] ?? false) === true) {
            throw new \InvalidArgumentException('No se permite autenticacion biometrica cross-origin.');
        }
    }

    private function parseAuthenticatorData(Request $request, string $authData, bool $requireAttestedCredential): array
    {
        if (strlen($authData) < 37) {
            throw new \InvalidArgumentException('Datos biometrico incompletos.');
        }

        $rpIdHash = substr($authData, 0, 32);
        $expectedRpIdHash = hash('sha256', $this->rpId($request), true);

        if (!hash_equals($expectedRpIdHash, $rpIdHash)) {
            throw new \InvalidArgumentException('El dispositivo no pertenece a este sitio.');
        }

        $flags = ord($authData[32]);

        if (($flags & 0x01) !== 0x01) {
            throw new \InvalidArgumentException('La presencia del usuario no fue confirmada.');
        }

        if (($flags & 0x04) !== 0x04) {
            throw new \InvalidArgumentException('La verificacion biometrica del usuario no fue confirmada.');
        }

        $signCount = unpack('N', substr($authData, 33, 4))[1];

        if (!$requireAttestedCredential) {
            return ['sign_count' => $signCount];
        }

        if (($flags & 0x40) !== 0x40) {
            throw new \InvalidArgumentException('El autenticador no envio una credencial nueva.');
        }

        $offset = 37 + 16;
        $credentialIdLength = unpack('n', substr($authData, $offset, 2))[1];
        $offset += 2;
        $credentialId = substr($authData, $offset, $credentialIdLength);
        $offset += $credentialIdLength;
        $publicKeyCose = substr($authData, $offset);

        if ($credentialId === '' || $publicKeyCose === '') {
            throw new \InvalidArgumentException('La credencial biometrica esta incompleta.');
        }

        return [
            'credential_id' => $credentialId,
            'public_key_cose' => $publicKeyCose,
            'sign_count' => $signCount,
        ];
    }

    private function coseToPem(string $cose): string
    {
        $key = (new CborDecoder())->decode($cose);
        $kty = $key[1] ?? null;

        if ($kty === 2) {
            return $this->ec2ToPem($key);
        }

        if ($kty === 3) {
            return $this->rsaToPem($key);
        }

        throw new \InvalidArgumentException('Tipo de llave biometrica no soportado.');
    }

    private function ec2ToPem(array $key): string
    {
        $x = $key[-2] ?? null;
        $y = $key[-3] ?? null;

        if (!is_string($x) || !is_string($y) || strlen($x) !== 32 || strlen($y) !== 32) {
            throw new \InvalidArgumentException('Llave EC biometrica invalida.');
        }

        $algorithm = $this->derSequence(
            $this->derOid('1.2.840.10045.2.1') .
            $this->derOid('1.2.840.10045.3.1.7')
        );
        $subjectPublicKey = $this->derBitString("\x04" . $x . $y);
        $spki = $this->derSequence($algorithm . $subjectPublicKey);

        return $this->pem($spki, 'PUBLIC KEY');
    }

    private function rsaToPem(array $key): string
    {
        $n = $key[-1] ?? null;
        $e = $key[-2] ?? null;

        if (!is_string($n) || !is_string($e)) {
            throw new \InvalidArgumentException('Llave RSA biometrica invalida.');
        }

        $rsaPublicKey = $this->derSequence($this->derInteger($n) . $this->derInteger($e));
        $algorithm = $this->derSequence(
            $this->derOid('1.2.840.113549.1.1.1') .
            "\x05\x00"
        );
        $spki = $this->derSequence($algorithm . $this->derBitString($rsaPublicKey));

        return $this->pem($spki, 'PUBLIC KEY');
    }

    private function derLength(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }

        $bytes = '';
        while ($length > 0) {
            $bytes = chr($length & 0xff) . $bytes;
            $length >>= 8;
        }

        return chr(0x80 | strlen($bytes)) . $bytes;
    }

    private function derSequence(string $value): string
    {
        return "\x30" . $this->derLength(strlen($value)) . $value;
    }

    private function derBitString(string $value): string
    {
        return "\x03" . $this->derLength(strlen($value) + 1) . "\x00" . $value;
    }

    private function derInteger(string $value): string
    {
        $value = ltrim($value, "\x00");

        if ($value === '') {
            $value = "\x00";
        }

        if ((ord($value[0]) & 0x80) !== 0) {
            $value = "\x00" . $value;
        }

        return "\x02" . $this->derLength(strlen($value)) . $value;
    }

    private function derOid(string $oid): string
    {
        $parts = array_map('intval', explode('.', $oid));
        $value = chr(($parts[0] * 40) + $parts[1]);

        for ($i = 2; $i < count($parts); $i++) {
            $value .= $this->base128($parts[$i]);
        }

        return "\x06" . $this->derLength(strlen($value)) . $value;
    }

    private function base128(int $value): string
    {
        $bytes = [chr($value & 0x7f)];
        $value >>= 7;

        while ($value > 0) {
            array_unshift($bytes, chr(0x80 | ($value & 0x7f)));
            $value >>= 7;
        }

        return implode('', $bytes);
    }

    private function pem(string $der, string $label): string
    {
        return "-----BEGIN {$label}-----\n" .
            chunk_split(base64_encode($der), 64, "\n") .
            "-----END {$label}-----\n";
    }
}
