<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileSecurityService
{
    /**
     * Daftar ekstensi berbahaya yang dilarang keras demi keamanan server dan asisten.
     */
    protected static array $dangerousExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
        'exe', 'bat', 'cmd', 'sh', 'bash', 'bin', 'cgi',
        'pl', 'py', 'jar', 'vbs', 'js', 'mjs', 'msi',
        'dll', 'scr', 'hta', 'ps1', 'com', 'pif', 'vbe',
        'wsf', 'wsc', 'reg', 'asp', 'aspx', 'jsp', 'jspx',
    ];

    /**
     * MIME types yang terindikasi file executable atau script berbahaya.
     */
    protected static array $dangerousMimes = [
        'application/x-dosexec',
        'application/x-msdownload',
        'application/x-executable',
        'application/x-sharedlib',
        'application/x-sh',
        'application/x-shellscript',
        'text/x-php',
        'application/x-php',
        'application/x-httpd-php',
        'text/x-script',
        'application/x-bat',
        'application/x-msi',
    ];

    /**
     * Memvalidasi file yang diunggah mahasiswa.
     *
     * @return array [bool $isValid, string|null $errorMessage]
     */
    public static function validate(UploadedFile $file, array $allowedExtensions = []): array
    {
        $originalName = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());

        // 1. Cek ekstensi utama
        if (in_array($ext, self::$dangerousExtensions, true)) {
            return [false, "Ekstensi file (.{$ext}) dilarang demi keamanan sistem."];
        }

        // 2. Cek serangan double-extension (misal: tugas.php.pdf)
        $parts = explode('.', $originalName);
        if (count($parts) > 2) {
            foreach (array_slice($parts, 1, -1) as $middleExt) {
                if (in_array(strtolower($middleExt), self::$dangerousExtensions, true)) {
                    return [false, "File memiliki format ekstensi ganda yang mencurigakan (.{$middleExt})."];
                }
            }
        }

        // 3. Cek MIME type riil dari konten file (Content Sniffing)
        $realPath = $file->getRealPath();
        if ($realPath && file_exists($realPath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = strtolower(finfo_file($finfo, $realPath) ?: '');
            finfo_close($finfo);

            if (in_array($realMime, self::$dangerousMimes, true)) {
                return [false, "Tipe konten file ({$realMime}) terdeteksi sebagai executable/script berbahaya."];
            }

            // 4. Deteksi Header Magic Bytes & Konten PHP/Shell Tersembunyi
            $handle = fopen($realPath, 'rb');
            if ($handle) {
                $header = fread($handle, 4096);
                fclose($handle);

                // Cek Magic Byte "MZ" (Windows Executable / DLL)
                if (str_starts_with($header, "MZ") && !in_array($ext, ['pkt', 'zip', 'rar'])) {
                    return [false, "File terdeteksi sebagai binary executable Windows (MZ Header)."];
                }

                // Cek Magic Byte ELF (Linux Executable) "\x7FELF"
                if (str_starts_with($header, "\x7FELF")) {
                    return [false, "File terdeteksi sebagai binary executable Linux (ELF Header)."];
                }

                // Cek tag PHP tersembunyi dalam file teks/gambar
                if (preg_match('/<\?(php|=|s)/i', $header)) {
                    return [false, "File mengandung script kode executable yang tidak diizinkan."];
                }
            }
        }

        // 5. Cek apakah format file sesuai dengan allowed_extensions soal (jika dispesifikasi dan bukan 'all')
        if (!empty($allowedExtensions) && !in_array('all', $allowedExtensions, true)) {
            $whitelistMimes = [];
            if (in_array('pdf', $allowedExtensions))     $whitelistMimes[] = 'pdf';
            if (in_array('word', $allowedExtensions))    $whitelistMimes = array_merge($whitelistMimes, ['doc', 'docx']);
            if (in_array('excel', $allowedExtensions))   $whitelistMimes = array_merge($whitelistMimes, ['xls', 'xlsx']);
            if (in_array('ppt', $allowedExtensions))     $whitelistMimes = array_merge($whitelistMimes, ['ppt', 'pptx']);
            if (in_array('image', $allowedExtensions))   $whitelistMimes = array_merge($whitelistMimes, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            if (in_array('archive', $allowedExtensions)) $whitelistMimes = array_merge($whitelistMimes, ['zip', 'rar', '7z', 'tar', 'gz']);
            if (in_array('pkt', $allowedExtensions))     $whitelistMimes[] = 'pkt';

            if (!in_array($ext, $whitelistMimes, true)) {
                return [false, "Format file (.{$ext}) tidak termasuk dalam format yang diizinkan untuk tugas ini."];
            }
        }

        return [true, null];
    }
}
