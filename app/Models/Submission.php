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

    public function isFile(): bool
    {
        return ($this->submission_type ?? 'file') === 'file';
    }

    public function isLink(): bool
    {
        return $this->submission_type === 'link';
    }

    public function isText(): bool
    {
        return $this->submission_type === 'text';
    }
}
