<?php

namespace App\Support\WebAuthn;

class CborDecoder
{
    public function decode(string $data): mixed
    {
        $offset = 0;
        return $this->read($data, $offset);
    }

    private function read(string $data, int &$offset): mixed
    {
        if ($offset >= strlen($data)) {
            throw new \InvalidArgumentException('CBOR incompleto.');
        }

        $initial = ord($data[$offset++]);
        $major = $initial >> 5;
        $additional = $initial & 0x1f;
        $length = $this->readLength($data, $offset, $additional);

        return match ($major) {
            0 => $length,
            1 => -1 - $length,
            2 => $this->readBytes($data, $offset, $length),
            3 => $this->readText($data, $offset, $length),
            4 => $this->readArray($data, $offset, $length),
            5 => $this->readMap($data, $offset, $length),
            6 => $this->read($data, $offset),
            7 => $this->readSimple($data, $offset, $additional),
            default => throw new \InvalidArgumentException('Tipo CBOR no soportado.'),
        };
    }

    private function readLength(string $data, int &$offset, int $additional): int
    {
        if ($additional < 24) {
            return $additional;
        }

        $bytes = match ($additional) {
            24 => 1,
            25 => 2,
            26 => 4,
            27 => 8,
            default => throw new \InvalidArgumentException('Longitud CBOR indefinida no soportada.'),
        };

        $value = 0;
        for ($i = 0; $i < $bytes; $i++) {
            if ($offset >= strlen($data)) {
                throw new \InvalidArgumentException('Longitud CBOR incompleta.');
            }

            $value = ($value << 8) | ord($data[$offset++]);
        }

        return $value;
    }

    private function readBytes(string $data, int &$offset, int $length): string
    {
        $value = substr($data, $offset, $length);

        if (strlen($value) !== $length) {
            throw new \InvalidArgumentException('Bytes CBOR incompletos.');
        }

        $offset += $length;

        return $value;
    }

    private function readText(string $data, int &$offset, int $length): string
    {
        return $this->readBytes($data, $offset, $length);
    }

    private function readArray(string $data, int &$offset, int $length): array
    {
        $items = [];

        for ($i = 0; $i < $length; $i++) {
            $items[] = $this->read($data, $offset);
        }

        return $items;
    }

    private function readMap(string $data, int &$offset, int $length): array
    {
        $map = [];

        for ($i = 0; $i < $length; $i++) {
            $key = $this->read($data, $offset);
            $map[$key] = $this->read($data, $offset);
        }

        return $map;
    }

    private function readSimple(string $data, int &$offset, int $additional): mixed
    {
        return match ($additional) {
            20 => false,
            21 => true,
            22, 23 => null,
            default => throw new \InvalidArgumentException('Valor simple CBOR no soportado.'),
        };
    }
}
