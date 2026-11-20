<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(function (Submission $submission) {
            foreach ($submission->files as $file) {
                if ($file->file_path) {
                    Storage::disk('private')->delete($file->file_path);
                }
            }
            if ($submission->file_path) {
                Storage::disk('private')->delete($submission->file_path);
            }
        });
    }

    protected $fillable = [
        'soal_id',
        'nim',
        'nama',
        'kelas',
        'submission_type',
        'link_url',
        'text_content',
        'file_path',
        'file_original_name',
        'file_extension',
        'file_size',
        'file_hash',
    ];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    /**
     * Mengambil seluruh berkas yang dikumpulkan mahasiswa.
     * Kompatibel dengan data lama (single file di submissions) maupun data baru (di submission_files).
     */
    public function getFilesList()
    {
        $hasFilesRelation = $this->relationLoaded('files')
            ? $this->files->isNotEmpty()
            : $this->files()->exists();

        if ($hasFilesRelation) {
            $files = $this->relationLoaded('files') ? $this->files : $this->files()->get();

            // Validasi integritas berkas terhadap identitas NIM mahasiswa
            if (!empty($this->file_path)) {
                $cleanNim = preg_replace('/[^a-zA-Z0-9\-]/', '', (string)$this->nim);
                $firstFilePath = (string)($files->first()?->file_path ?? '');

                // Jika berkas relasi tidak memuat NIM mahasiswa namun berkas submissions memuat NIM yang sesuai,
                // gunakan berkas primer pada submissions untuk mencegah kekeliruan akibat pergeseran ID relasi.
                if ($cleanNim !== '' && !str_contains($firstFilePath, $cleanNim) && str_contains($this->file_path, $cleanNim)) {
                    return collect([
                        (object) [
                            'id'                 => 0,
                            'file_path'          => $this->file_path,
                            'file_original_name' => $this->file_original_name ?: basename($this->file_path),
                            'file_extension'     => $this->file_extension ?: pathinfo($this->file_path, PATHINFO_EXTENSION),
                            'file_size'          => $this->file_size ?: 0,
                        ]
                    ]);
                }
            }

            return $files;
        }

        if (!empty($this->file_path)) {
            return collect([
                (object) [
                    'id'                 => 0,
                    'file_path'          => $this->file_path,
                    'file_original_name' => $this->file_original_name ?: basename($this->file_path),
                    'file_extension'     => $this->file_extension ?: pathinfo($this->file_path, PATHINFO_EXTENSION),
                    'file_size'          => $this->file_size ?: 0,
                ]
            ]);
        }

        return collect([]);
    }

    /**
     * Hitung total berkas yang dikumpulkan.
     */
    public function getFilesCount(): int
    {
        $count = $this->relationLoaded('files') ? $this->files->count() : $this->files()->count();
        if ($count > 0) {
            return $count;
        }

        return !empty($this->file_path) ? 1 : 0;
    }

    public function hasFile(): bool
    {
        return !empty($this->file_path) || $this->getFilesCount() > 0;
    }

    public function hasLink(): bool
    {
        return !empty($this->link_url) && $this->getLinksCount() > 0;
    }

    /**
     * Mengambil seluruh daftar tautan link tugas mahasiswa.
     * Mendukung data lama (string URL tunggal) maupun format baru (JSON array tautan link).
     *
     * @return array<int, array{url: string, title: ?string}>
     */
    public function getLinksList(): array
    {
        if (empty($this->link_url)) {
            return [];
        }

        $val = trim((string)$this->link_url);

        // Cek format JSON array
        if ((str_starts_with($val, '[') && str_ends_with($val, ']')) || (str_starts_with($val, '{') && str_ends_with($val, '}'))) {
            $decoded = json_decode($val, true);
            if (is_array($decoded)) {
                $list = [];
                foreach ($decoded as $item) {
                    if (is_string($item) && !empty(trim($item))) {
                        $list[] = ['url' => trim($item), 'title' => null];
                    } elseif (is_array($item) && !empty($item['url'])) {
                        $list[] = ['url' => trim($item['url']), 'title' => !empty($item['title']) ? trim($item['title']) : null];
                    }
                }
                if (!empty($list)) {
                    return $list;
                }
            }
        }

        // Cek baris per baris (jika dipisahkan newline)
        $lines = preg_split('/[\r\n]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
        if (count($lines) > 1) {
            $list = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $list[] = ['url' => $line, 'title' => null];
                }
            }
            if (!empty($list)) {
                return $list;
            }
        }

        return [['url' => $val, 'title' => null]];
    }

    public function getLinksCount(): int
    {
        return count($this->getLinksList());
    }

    public function getFirstLinkUrl(): ?string
    {
        $links = $this->getLinksList();
        return $links[0]['url'] ?? $this->link_url;
    }

    public function hasText(): bool
    {
        return !empty($this->text_content);
    }

    public function isComposite(): bool
    {
        $count = ($this->hasFile() ? 1 : 0) + ($this->hasLink() ? 1 : 0) + ($this->hasText() ? 1 : 0);
        return $count > 1;
    }

    public function isFile(): bool
    {
        return $this->hasFile() || ($this->submission_type ?? 'file') === 'file';
    }

    public function isLink(): bool
    {
        return $this->hasLink() || $this->submission_type === 'link';
    }

    public function isText(): bool
    {
        return $this->hasText() || $this->submission_type === 'text';
    }
}
