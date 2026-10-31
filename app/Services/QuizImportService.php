<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class QuizImportService
{
    /**
     * Download sample CSV template with UTF-8 BOM for Microsoft Excel compatibility.
     * Supports both semicolon ';' (optimal for MS Excel Indonesia) and comma ',' (standard CSV).
     */
    public static function downloadTemplate(string $delimiter = ';'): StreamedResponse
    {
        $suffix = $delimiter === ';' ? 'excel_titikkoma' : 'standar_koma';
        $filename = 'template_bank_soal_' . $suffix . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($delimiter) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Microsoft Excel & Google Sheets compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header columns
            fputcsv($handle, [
                'Nomor',
                'Pertanyaan',
                'Tipe_Soal',
                'Poin',
                'Pilihan_A',
                'Pilihan_B',
                'Pilihan_C',
                'Pilihan_D',
                'Pilihan_E',
                'Kunci_Jawaban',
                'Pembahasan'
            ], $delimiter);

            // Sample 1: Pilihan Ganda Tunggal (dengan Blok Kode Program C++)
            fputcsv($handle, [
                1,
                "Perhatikan potongan kode C++ berikut:\n```cpp\nint a = 15;\nint *ptr = &a;\n*ptr = *ptr + 10;\ncout << a;\n```\nBerapakah nilai variabel `a` yang tercetak pada layar?",
                'multiple_choice',
                10,
                '15',
                '25',
                'Alamat memori &a',
                '0',
                '',
                'B',
                'Operator dereference (*ptr) memodifikasi nilai memori variabel a langsung, sehingga a menjadi 15 + 10 = 25.'
            ], $delimiter);

            // Sample 2: Pilihan Ganda Tunggal (Konsep Teori)
            fputcsv($handle, [
                2,
                'Fungsi bawaan dalam bahasa C yang bertugas mengalokasikan memori dinamis di area heap adalah...',
                'multiple_choice',
                10,
                'malloc()',
                'free()',
                'sizeof()',
                'realloc()',
                'calloc()',
                'A',
                'Fungsi malloc() (memory allocation) mengalokasikan sejumlah byte memori di heap tanpa inisialisasi nilai.'
            ], $delimiter);

            // Sample 3: Multi-Select / Pilihan Jamak (Kunci Jawaban Gabungan)
            fputcsv($handle, [
                3,
                'Manakah pernyataan berikut yang BENAR mengenai pointer dan array dalam bahasa C? (Centang semua jawaban benar)',
                'multi_select',
                15,
                'Nama array bertindak sebagai pointer konstan yang menunjuk ke elemen pertama (indeks 0)',
                'Array tidak dapat diakses menggunakan notasi pointer (*arr)',
                'Operasi aritmatika pointer (ptr++) melompat sebesar ukuran tipe data elemen',
                'Ukuran array dihitung otomatis saat runtime melalui sizeof(pointer)',
                '',
                'A, C',
                'Pernyataan A dan C benar. Nama array merujuk alamat elemen pertama & pointer arithmetic melompat sesuai ukuran tipe data.'
            ], $delimiter);

            // Sample 4: True / False (Benar / Salah)
            fputcsv($handle, [
                4,
                'Dalam bahasa pemrograman C, indeks array selalu dimulai dari 0 (zero-indexed) dan alokasi memorinya bersifat kontigu (bersebelahan).',
                'true_false',
                10,
                'Benar',
                'Salah',
                '',
                '',
                '',
                'A',
                'Benar. Array dalam C selalu dimulai dari indeks 0 dan setiap elemen disimpan pada alamat memori yang berurutan.'
            ], $delimiter);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import questions from CSV or XLSX file.
     */
    public static function import(Quiz $quiz, UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if (in_array($ext, ['csv', 'txt'])) {
            $rows = self::parseCsv($file->getRealPath());
        } elseif ($ext === 'xlsx') {
            $rows = self::parseXlsx($file->getRealPath());
        } else {
            return [
                'success' => false,
                'message' => 'Format file tidak didukung. Harap gunakan file .csv atau .xlsx spreadsheet.',
                'count'   => 0,
            ];
        }

        if (empty($rows)) {
            return [
                'success' => false,
                'message' => 'File spreadsheet kosong atau tidak berisi data yang dapat dibaca.',
                'count'   => 0,
            ];
        }

        // Header detection (first row)
        $header = array_map(fn($h) => strtolower(trim((string)$h)), array_shift($rows));
        $colMap = self::mapColumns($header);

        if ($colMap['question'] === null || $colMap['key'] === null) {
            return [
                'success' => false,
                'message' => 'Format kolom spreadsheet tidak sesuai. Pastikan terdapat kolom "Pertanyaan" dan "Kunci_Jawaban". Silakan unduh template resmi.',
                'count'   => 0,
            ];
        }

        $importedCount = 0;
        $maxOrder = (int) ($quiz->questions()->max('order_num') ?? 0);

        DB::transaction(function () use ($quiz, $rows, $colMap, &$importedCount, &$maxOrder) {
            foreach ($rows as $rowIndex => $row) {
                $questionText = trim($row[$colMap['question']] ?? '');
                if (empty($questionText)) {
                    continue; // Skip empty questions
                }

                $points = 10;
                if ($colMap['points'] !== null && !empty($row[$colMap['points']])) {
                    $pVal = (int) preg_replace('/[^0-9]/', '', (string)$row[$colMap['points']]);
                    if ($pVal > 0) $points = $pVal;
                }

                $rawType = $colMap['type'] !== null ? strtolower(trim((string)($row[$colMap['type']] ?? ''))) : '';
                $rawKey = $colMap['key'] !== null ? trim((string)($row[$colMap['key']] ?? '')) : 'A';
                $explanation = $colMap['explanation'] !== null ? trim((string)($row[$colMap['explanation']] ?? '')) : null;

                // Collect options A-E
                $optionsData = [];
                foreach (['a', 'b', 'c', 'd', 'e'] as $letter) {
                    if (isset($colMap['opt_' . $letter]) && $colMap['opt_' . $letter] !== null) {
                        $optText = trim((string)($row[$colMap['opt_' . $letter]] ?? ''));
                        if ($optText !== '') {
                            $optionsData[$letter] = $optText;
                        }
                    }
                }

                // If only 2 options provided and type is true_false
                if (count($optionsData) < 2) {
                    continue; // Skip if less than 2 options
                }

                // Parse correct letters
                $correctLetters = self::parseCorrectKeys($rawKey);
                $isMulti = count($correctLetters) > 1 || in_array($rawType, ['multi_select', 'jamak', 'pilihan_jamak']);
                $questionType = $isMulti ? 'multi_select' : ($rawType === 'true_false' || in_array($rawType, ['benar_salah', 'tf']) ? 'true_false' : 'multiple_choice');

                $maxOrder++;
                $question = $quiz->questions()->create([
                    'question_text' => $questionText,
                    'question_type' => $questionType,
                    'points'        => $points,
                    'explanation'   => $explanation,
                    'order_num'     => $maxOrder,
                ]);

                $optOrder = 1;
                foreach ($optionsData as $letter => $text) {
                    $isCorrect = in_array($letter, $correctLetters, true);
                    $question->options()->create([
                        'option_text' => $text,
                        'is_correct'  => $isCorrect,
                        'order_num'   => $optOrder++,
                    ]);
                }

                $importedCount++;
            }
        });

        if ($importedCount === 0) {
            return [
                'success' => false,
                'message' => 'Tidak ada soal valid yang berhasil diimpor. Periksa kembali kelengkapan data soal pada spreadsheet.',
                'count'   => 0,
            ];
        }

        return [
            'success' => true,
            'message' => "Berhasil mengimpor {$importedCount} butir pertanyaan ke dalam bank soal kuis!",
            'count'   => $importedCount,
        ];
    }

    /**
     * Parse CSV file with automatic delimiter detection, UTF-8 BOM handling,
     * and multiline cell support for questions containing code blocks or line breaks.
     */
    private static function parseCsv(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [];
        }

        // Auto-detect delimiter from first line
        $firstLine = fgets($handle);
        $delimiter = ',';
        if ($firstLine !== false) {
            $bom = "\xEF\xBB\xBF";
            if (str_starts_with($firstLine, $bom)) {
                $firstLine = substr($firstLine, 3);
            }
            $semicolons = substr_count($firstLine, ';');
            $commas = substr_count($firstLine, ',');
            $tabs = substr_count($firstLine, "\t");
            if ($semicolons > $commas && $semicolons > $tabs) {
                $delimiter = ';';
            } elseif ($tabs > $commas && $tabs > $semicolons) {
                $delimiter = "\t";
            }
        }
        rewind($handle);

        // Strip UTF-8 BOM at start of stream if present
        $bomCheck = fread($handle, 3);
        if ($bomCheck !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rows = [];
        // fgetcsv natively reads multiline quoted fields across multiple lines!
        while (($row = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            // Skip empty rows
            if (empty($row) || (count($row) === 1 && ($row[0] === null || trim((string)$row[0]) === ''))) {
                continue;
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * Parse XLSX using native PHP ZipArchive and XML parsing (zero dependency).
     */
    private static function parseXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read first worksheet
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            return [];
        }

        $xml = @simplexml_load_string($sheetXml);
        $zip->close();

        if (!$xml || !isset($xml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            $colIndex = 0;

            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                preg_match('/([A-Z]+)(\d+)/', $cellRef, $matches);
                $colLetters = $matches[1] ?? 'A';
                $targetCol = self::columnLetterToIndex($colLetters);

                // Pad missing empty cells
                while ($colIndex < $targetCol) {
                    $rowData[] = '';
                    $colIndex++;
                }

                $type = (string) $cell['t'];
                $val = isset($cell->v) ? (string) $cell->v : '';

                if ($type === 's' && isset($sharedStrings[(int) $val])) {
                    $val = $sharedStrings[(int) $val];
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                }

                $rowData[] = $val;
                $colIndex++;
            }

            if (!empty(array_filter($rowData, fn($c) => trim($c) !== ''))) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    private static function columnLetterToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $len = strlen($letters);
        $index = 0;
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }
        return $index - 1;
    }

    private static function mapColumns(array $headers): array
    {
        $map = [
            'question'    => null,
            'type'        => null,
            'points'      => null,
            'opt_a'       => null,
            'opt_b'       => null,
            'opt_c'       => null,
            'opt_d'       => null,
            'opt_e'       => null,
            'key'         => null,
            'explanation' => null,
        ];

        foreach ($headers as $idx => $name) {
            $nameClean = strtolower(str_replace([' ', '_', '-'], '', $name));

            if (in_array($nameClean, ['pertanyaan', 'soal', 'question', 'soalpertanyaan'])) {
                $map['question'] = $idx;
            } elseif (in_array($nameClean, ['tipe', 'tipesoal', 'type', 'questiontype'])) {
                $map['type'] = $idx;
            } elseif (in_array($nameClean, ['poin', 'point', 'points', 'bobot', 'skor'])) {
                $map['points'] = $idx;
            } elseif (in_array($nameClean, ['pilihana', 'opsia', 'optiona', 'a'])) {
                $map['opt_a'] = $idx;
            } elseif (in_array($nameClean, ['pilihanb', 'opsib', 'optionb', 'b'])) {
                $map['opt_b'] = $idx;
            } elseif (in_array($nameClean, ['pilihanc', 'opsic', 'optionc', 'c'])) {
                $map['opt_c'] = $idx;
            } elseif (in_array($nameClean, ['pilihand', 'opsid', 'optiond', 'd'])) {
                $map['opt_d'] = $idx;
            } elseif (in_array($nameClean, ['pilihane', 'opsie', 'optione', 'e'])) {
                $map['opt_e'] = $idx;
            } elseif (in_array($nameClean, ['kunci', 'kuncijawaban', 'jawabanbenar', 'correct', 'answer', 'key'])) {
                $map['key'] = $idx;
            } elseif (in_array($nameClean, ['pembahasan', 'penjelasan', 'explanation'])) {
                $map['explanation'] = $idx;
            }
        }

        return $map;
    }

    private static function parseCorrectKeys(string $rawKey): array
    {
        $rawKey = strtoupper(trim($rawKey));
        $keys = [];

        // Match individual letters A, B, C, D, E or numbers 1, 2, 3, 4, 5
        $parts = preg_split('/[,;\s|\/]+/', $rawKey);
        $numToLetter = ['1' => 'a', '2' => 'b', '3' => 'c', '4' => 'd', '5' => 'e'];

        foreach ($parts as $p) {
            $p = strtolower(trim($p));
            if (isset($numToLetter[$p])) {
                $keys[] = $numToLetter[$p];
            } elseif (in_array($p, ['a', 'b', 'c', 'd', 'e'])) {
                $keys[] = $p;
            }
        }

        if (empty($keys) && !empty($rawKey)) {
            // Check if user wrote 'A' or 'True' or 'Benar'
            if (str_starts_with(strtolower($rawKey), 'b') || str_starts_with(strtolower($rawKey), 't')) {
                $keys[] = 'a'; // Benar / True = Opsi A
            } elseif (str_starts_with(strtolower($rawKey), 's') || str_starts_with(strtolower($rawKey), 'f')) {
                $keys[] = 'b'; // Salah / False = Opsi B
            } else {
                $firstChar = strtolower($rawKey[0]);
                if (in_array($firstChar, ['a', 'b', 'c', 'd', 'e'])) {
                    $keys[] = $firstChar;
                }
            }
        }

        return array_unique($keys ?: ['a']);
    }
}
