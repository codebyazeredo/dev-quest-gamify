<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvDownload
{
    private const DELIMITER = ';';

    private const FORMULA_PREFIXES = ['=', '+', '-', '@', "\t", "\r"];

    /**
     * @param  array<int, string>  $header
     * @param  iterable<int, array<int, scalar|null>>  $rows
     */
    public static function make(string $filename, array $header, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $output = fopen('php://output', 'w');

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $header, self::DELIMITER);

            foreach ($rows as $row) {
                fputcsv($output, array_map(self::neutralizeFormula(...), $row), self::DELIMITER);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private static function neutralizeFormula(mixed $cell): mixed
    {
        if (is_string($cell) && $cell !== '' && in_array($cell[0], self::FORMULA_PREFIXES, true)) {
            return "'".$cell;
        }

        return $cell;
    }
}
