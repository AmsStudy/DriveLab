<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class QuizImportService
{
    /**
     * Download native Microsoft Excel (.xlsx) template with in-cell dropdown data validations
     * on choice columns (Tipe_Soal: multiple_choice, multi_select, true_false; Kunci_Jawaban: A-E, Benar, Salah).
     */
    public static function downloadXlsxTemplate(): BinaryFileResponse
    {
        $filename = 'template_bank_soal_excel_interaktif_dropdown_' . date('Ymd') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'template_bank_soal_') . '.xlsx';

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file template Excel.');
        }

        $typesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

        $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

        $wbRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
</Relationships>';

        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Bank Soal" sheetId="1" r:id="rId1"/>
    <sheet name="Panduan Kolom" sheetId="2" r:id="rId3"/>
  </sheets>
</workbook>';

        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><sz val="10"/><color rgb="FF1F2937"/><name val="Calibri"/></font>
    <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF0D9488"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/><diagonal/></border>
    <border>
      <left style="thin"><color rgb="FFCBD5E1"/></left>
      <right style="thin"><color rgb="FFCBD5E1"/></right>
      <top style="thin"><color rgb="FFCBD5E1"/></top>
      <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="3">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
      <alignment horizontal="center" vertical="center" wrapText="1"/>
    </xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">
      <alignment vertical="top" wrapText="1"/>
    </xf>
  </cellXfs>
</styleSheet>';

        // Sheet 1 Data
        $s1Rows = [
            ['Nomor', 'Pertanyaan', 'Tipe_Soal', 'Poin', 'Pilihan_A', 'Pilihan_B', 'Pilihan_C', 'Pilihan_D', 'Pilihan_E', 'Kunci_Jawaban', 'Pembahasan'],
            [
                1,
                "Perhatikan potongan kode C++ berikut:\n```cpp\nint a = 15;\nint *ptr = &a;\n*ptr = *ptr + 10;\ncout << a;\n```\nBerapakah nilai variabel `a` yang tercetak pada layar?",
                'multiple_choice',
                10,
                '15', '25', 'Alamat memori &a', '0', '',
                'B',
                'Operator dereference (*ptr) memodifikasi nilai memori variabel a langsung, sehingga a menjadi 15 + 10 = 25.'
            ],
            [
                2,
                'Fungsi bawaan dalam bahasa C yang bertugas mengalokasikan memori dinamis di area heap adalah...',
                'multiple_choice',
                10,
                'malloc()', 'free()', 'sizeof()', 'realloc()', 'calloc()',
                'A',
                'Fungsi malloc() (memory allocation) mengalokasikan sejumlah byte memori di heap tanpa inisialisasi nilai.'
            ],
            [
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
            ],
            [
                4,
                'Dalam bahasa pemrograman C, indeks array selalu dimulai dari 0 (zero-indexed) dan alokasi memorinya bersifat kontigu (bersebelahan).',
                'true_false',
                10,
                'Benar', 'Salah', '', '', '',
                'A',
                'Benar. Array dalam C selalu dimulai dari indeks 0 dan setiap elemen disimpan pada alamat memori yang berurutan.'
            ]
        ];

        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
        $s1Data = '<sheetData>';
        foreach ($s1Rows as $rIdx => $rVals) {
            $rNum = $rIdx + 1;
            $sId = ($rNum === 1) ? 1 : 2;
            $s1Data .= '<row r="' . $rNum . '">';
            foreach ($rVals as $cIdx => $v) {
                $cRef = $columns[$cIdx] . $rNum;
                if (is_int($v)) {
                    $s1Data .= '<c r="' . $cRef . '" s="' . $sId . '"><v>' . $v . '</v></c>';
                } else {
                    $escaped = htmlspecialchars((string)$v, ENT_XML1, 'UTF-8');
                    $s1Data .= '<c r="' . $cRef . '" t="inlineStr" s="' . $sId . '"><is><t xml:space="preserve">' . $escaped . '</t></is></c>';
                }
            }
            $s1Data .= '</row>';
        }
        $s1Data .= '</sheetData>';

        $sheet1Xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:K100"/>
  <sheetViews>
    <sheetView tabSelected="1" workbookViewId="0">
      <pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>
    </sheetView>
  </sheetViews>
  <sheetFormatPr defaultRowHeight="20"/>
  <cols>
    <col min="1" max="1" width="8" customWidth="1"/>
    <col min="2" max="2" width="55" customWidth="1"/>
    <col min="3" max="3" width="20" customWidth="1"/>
    <col min="4" max="4" width="10" customWidth="1"/>
    <col min="5" max="5" width="26" customWidth="1"/>
    <col min="6" max="6" width="26" customWidth="1"/>
    <col min="7" max="7" width="26" customWidth="1"/>
    <col min="8" max="8" width="26" customWidth="1"/>
    <col min="9" max="9" width="26" customWidth="1"/>
    <col min="10" max="10" width="18" customWidth="1"/>
    <col min="11" max="11" width="38" customWidth="1"/>
  </cols>
  ' . $s1Data . '
  <dataValidations count="2">
    <dataValidation type="list" allowBlank="1" showInputMessage="1" showErrorMessage="1" sqref="C2:C500" promptTitle="Pilih Tipe Soal" prompt="Pilih salah satu: multiple_choice, multi_select, true_false" errorTitle="Tipe Soal Tidak Valid" error="Pilih salah satu tipe soal yang tersedia pada dropdown.">
      <formula1>&quot;multiple_choice,multi_select,true_false&quot;</formula1>
    </dataValidation>
    <dataValidation type="list" allowBlank="1" showInputMessage="1" showErrorMessage="1" sqref="J2:J500" promptTitle="Pilih Kunci Jawaban" prompt="Pilih huruf kunci jawaban yang sesuai dari dropdown" errorTitle="Kunci Tidak Valid" error="Pilih huruf jawaban benar dari dropdown list.">
      <formula1>&quot;A,B,C,D,E,Benar,Salah&quot;</formula1>
    </dataValidation>
  </dataValidations>
  <pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>
</worksheet>';

        // Sheet 2 Data: Panduan Kolom
        $s2Rows = [
            ['Nama Kolom', 'Kewajiban', 'Tipe Data / Pilihan', 'Keterangan'],
            ['Nomor', 'Opsional', 'Angka Urut', 'Nomor butir pertanyaan (1, 2, 3...)'],
            ['Pertanyaan', 'Wajib', 'Teks / Markdown', 'Pertanyaan soal. Mendukung blok kode program ```cpp, ```python, tabel, dan format teks.'],
            ['Tipe_Soal', 'Wajib (Dropdown)', 'multiple_choice | multi_select | true_false', 'Gunakan pilihan pada dropdown kolom C untuk memilih jenis soal tanpa mengetik.'],
            ['Poin', 'Opsional', 'Angka Bulat Positif', 'Bobot nilai soal (default: 10 jika dikosongkan).'],
            ['Pilihan_A', 'Wajib', 'Teks Pilihan A', 'Opsi jawaban pertama (atau \'Benar\' untuk true_false).'],
            ['Pilihan_B', 'Wajib', 'Teks Pilihan B', 'Opsi jawaban kedua (atau \'Salah\' untuk true_false).'],
            ['Pilihan_C', 'Opsional', 'Teks Pilihan C', 'Opsi jawaban ketiga (opsional untuk pilihan ganda).'],
            ['Pilihan_D', 'Opsional', 'Teks Pilihan D', 'Opsi jawaban keempat.'],
            ['Pilihan_E', 'Opsional', 'Teks Pilihan E', 'Opsi jawaban kelima.'],
            ['Kunci_Jawaban', 'Wajib (Dropdown)', 'A, B, C, D, E, Benar, Salah', 'Pilih huruf kunci jawaban dari dropdown kolom J.'],
            ['Pembahasan', 'Opsional', 'Teks Penjelasan', 'Catatan atau penjelasan kunci jawaban saat mahasiswa meninjau nilai ujian.'],
        ];

        $s2Cols = ['A', 'B', 'C', 'D'];
        $s2Data = '<sheetData>';
        foreach ($s2Rows as $rIdx => $rVals) {
            $rNum = $rIdx + 1;
            $sId = ($rNum === 1) ? 1 : 2;
            $s2Data .= '<row r="' . $rNum . '">';
            foreach ($rVals as $cIdx => $v) {
                $cRef = $s2Cols[$cIdx] . $rNum;
                $escaped = htmlspecialchars((string)$v, ENT_XML1, 'UTF-8');
                $s2Data .= '<c r="' . $cRef . '" t="inlineStr" s="' . $sId . '"><is><t xml:space="preserve">' . $escaped . '</t></is></c>';
            }
            $s2Data .= '</row>';
        }
        $s2Data .= '</sheetData>';

        $sheet2Xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <dimension ref="A1:D15"/>
  <sheetFormatPr defaultRowHeight="20"/>
  <cols>
    <col min="1" max="1" width="20" customWidth="1"/>
    <col min="2" max="2" width="20" customWidth="1"/>
    <col min="3" max="3" width="38" customWidth="1"/>
    <col min="4" max="4" width="60" customWidth="1"/>
  </cols>
  ' . $s2Data . '
  <pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>
</worksheet>';

        $zip->addFromString('[Content_Types].xml', $typesXml);
        $zip->addFromString('_rels/.rels', $relsXml);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRelsXml);
        $zip->addFromString('xl/workbook.xml', $workbookXml);
        $zip->addFromString('xl/styles.xml', $stylesXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1Xml);
        $zip->addFromString('xl/worksheets/sheet2.xml', $sheet2Xml);
        $zip->close();

        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->download($tempFile, $filename, $headers)->deleteFileAfterSend(true);
    }

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
