<?php

namespace App;

use SplFileObject;

final class Parser
{
    public function parse(string $inputPath, string $outputPath): void
    {
        $file = new SplFileObject($inputPath);
        $file->setCsvControl(separator: ',', escape: "\\");

        $map = [];

        while (($row = $file->fgetcsv()) !== false) {
            if ($row === [null]) {
                continue;
            }

            // Echo one line from the file.
            [$key, $day] = $row;

            // Get path
            $key = parse_url($key, PHP_URL_PATH) ?? '/';

            // Get date
            $day = substr($day, 0, 10);

            if (!($map[$key] ?? false)) {
                $map[$key] = [
                    $day => 1
                ];
            } else {
                if (!($map[$key][$day] ?? false)) {
                    $map[$key][$day] = 1;
                } else {
                    $map[$key][$day]++;
                }
            }
        }

        // Sort visits per day (required)
        foreach ($map as &$path) {
            if (count($path) > 1) {
                ksort($path, SORT_STRING);
            }
        }

        file_put_contents($outputPath, json_encode($map, JSON_PRETTY_PRINT));

        // Close pointer
        $file = null;
    }
}