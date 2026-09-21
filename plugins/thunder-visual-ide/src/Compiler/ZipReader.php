<?php

declare(strict_types=1);

namespace ThunderVisualIde\Compiler;

use RuntimeException;

/**
 * Small ZIP reader used for bundled library packages.
 *
 * It supports normal stored and deflated ZIP entries. Zip64 and encrypted
 * archives are intentionally rejected so project builds remain predictable.
 */
final class ZipReader
{
    /**
     * @return array<string,string>
     */
    public static function files(string $archive, bool $stripCommonRoot = true): array
    {
        if ($archive === '') {
            return [];
        }

        $directory = self::centralDirectory($archive);
        $offset = $directory['offset'];
        $files = [];

        for ($index = 0; $index < $directory['entries']; $index++) {
            if (substr($archive, $offset, 4) !== "PK\x01\x02") {
                throw new RuntimeException('The library ZIP central directory is invalid.');
            }

            $method = self::uint16($archive, $offset + 10);
            $flags = self::uint16($archive, $offset + 8);
            $compressedSize = self::uint32($archive, $offset + 20);
            $nameLength = self::uint16($archive, $offset + 28);
            $extraLength = self::uint16($archive, $offset + 30);
            $commentLength = self::uint16($archive, $offset + 32);
            $localOffset = self::uint32($archive, $offset + 42);
            $name = substr($archive, $offset + 46, $nameLength);
            $offset += 46 + $nameLength + $extraLength + $commentLength;

            if (($flags & 0x0001) !== 0) {
                throw new RuntimeException('Encrypted library ZIP archives are not supported.');
            }
            if ($compressedSize === 0xffffffff || $localOffset === 0xffffffff) {
                throw new RuntimeException('Zip64 library archives are not supported.');
            }

            $path = self::safePath($name);
            if ($path === '' || str_ends_with($path, '/')) {
                continue;
            }
            if (substr($archive, $localOffset, 4) !== "PK\x03\x04") {
                throw new RuntimeException("The local ZIP header for {$path} is invalid.");
            }

            $localNameLength = self::uint16($archive, $localOffset + 26);
            $localExtraLength = self::uint16($archive, $localOffset + 28);
            $dataOffset = $localOffset + 30 + $localNameLength + $localExtraLength;
            $compressed = substr($archive, $dataOffset, $compressedSize);

            $content = match ($method) {
                0 => $compressed,
                8 => gzinflate($compressed),
                default => throw new RuntimeException(
                    "Library ZIP entry {$path} uses unsupported compression method {$method}."
                ),
            };

            if ($content === false) {
                throw new RuntimeException("Unable to decompress library ZIP entry {$path}.");
            }
            $files[$path] = $content;
        }

        return $stripCommonRoot ? self::stripCommonRoot($files) : $files;
    }

    /** @return array{entries:int,offset:int} */
    private static function centralDirectory(string $archive): array
    {
        $minimum = max(0, strlen($archive) - 65_557);
        $position = strrpos(substr($archive, $minimum), "PK\x05\x06");
        if ($position === false) {
            throw new RuntimeException('The uploaded library file is not a valid ZIP archive.');
        }
        $position += $minimum;

        return [
            'entries' => self::uint16($archive, $position + 10),
            'offset' => self::uint32($archive, $position + 16),
        ];
    }

    private static function uint16(string $data, int $offset): int
    {
        return (int) unpack('vvalue', substr($data, $offset, 2))['value'];
    }

    private static function uint32(string $data, int $offset): int
    {
        return (int) unpack('Vvalue', substr($data, $offset, 4))['value'];
    }

    private static function safePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                throw new RuntimeException('The library ZIP contains an unsafe parent path.');
            }
            $parts[] = preg_replace('/[\x00-\x1F]/', '', $part) ?? '';
        }
        return implode('/', array_filter($parts, static fn (string $part): bool => $part !== ''));
    }

    /**
     * @param array<string,string> $files
     * @return array<string,string>
     */
    private static function stripCommonRoot(array $files): array
    {
        if ($files === []) {
            return [];
        }

        $roots = [];
        foreach (array_keys($files) as $path) {
            $parts = explode('/', $path, 2);
            if (count($parts) < 2) {
                return $files;
            }
            $roots[$parts[0]] = true;
        }
        if (count($roots) !== 1) {
            return $files;
        }

        $result = [];
        foreach ($files as $path => $content) {
            $trimmed = explode('/', $path, 2)[1] ?? '';
            if ($trimmed !== '') {
                $result[$trimmed] = $content;
            }
        }
        return $result;
    }
}
