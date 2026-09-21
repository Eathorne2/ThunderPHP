<?php

declare(strict_types=1);

namespace ThunderVisualIde;

/**
 * Small ZIP writer that does not require the ZipArchive PHP extension.
 * Files are stored without compression for maximum compatibility.
 */
final class ZipWriter
{
    /**
     * @param array<string,string> $files
     */
    public static function create(array $files, string $rootDirectory = ''): string
    {
        $localData = '';
        $centralData = '';
        $offset = 0;
        $count = 0;
        $timestamp = self::dosTimestamp(time());

        $rootDirectory = trim(str_replace('\\', '/', $rootDirectory), '/');

        foreach ($files as $path => $contents) {
            $path = ltrim(str_replace('\\', '/', $path), '/');
            $name = $rootDirectory !== '' ? $rootDirectory . '/' . $path : $path;
            $name = self::safePath($name);

            if ($name === '') {
                continue;
            }

            $contents = (string) $contents;
            $crc = crc32($contents);
            $size = strlen($contents);
            $nameLength = strlen($name);

            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0,
                0,
                $timestamp['time'],
                $timestamp['date'],
                $crc,
                $size,
                $size,
                $nameLength,
                0
            );

            $localData .= $localHeader . $name . $contents;

            $centralHeader = pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0,
                0,
                $timestamp['time'],
                $timestamp['date'],
                $crc,
                $size,
                $size,
                $nameLength,
                0,
                0,
                0,
                0,
                0,
                $offset
            );

            $centralData .= $centralHeader . $name;
            $offset = strlen($localData);
            $count++;
        }

        $end = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            $count,
            $count,
            strlen($centralData),
            strlen($localData),
            0
        );

        return $localData . $centralData . $end;
    }

    private static function safePath(string $path): string
    {
        $parts = [];

        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($parts);
                continue;
            }

            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    /** @return array{time:int,date:int} */
    private static function dosTimestamp(int $timestamp): array
    {
        $parts = getdate($timestamp);
        $year = max(1980, (int) $parts['year']);

        return [
            'time' => ((int) $parts['hours'] << 11)
                | ((int) $parts['minutes'] << 5)
                | ((int) floor((int) $parts['seconds'] / 2)),
            'date' => (($year - 1980) << 9)
                | ((int) $parts['mon'] << 5)
                | (int) $parts['mday'],
        ];
    }
}
