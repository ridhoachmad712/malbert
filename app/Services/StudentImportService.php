<?php

namespace App\Services;

use App\Http\Requests\SaveStudentRequest;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentImportService
{
    public function __construct(private StudentRegistry $registry) {}

    public function import(User $user, int $classroomId, string $date, UploadedFile $file): int
    {
        try {
            return DB::transaction(function () use ($user, $classroomId, $date, $file) {
                $classroom = $this->registry->classroom($user, $classroomId);
                $this->registry->validateDate($classroom, $date);
                $contents = file_get_contents($file->getRealPath());
                if (! mb_check_encoding($contents, 'UTF-8') || str_contains($contents, "\0")) {
                    throw ValidationException::withMessages(['file' => 'Simpan berkas sebagai CSV UTF-8, lalu unggah kembali.']);
                }
                $stream = fopen($file->getRealPath(), 'rb');
                $rows = [];
                $errors = [];
                $seen = ['nis' => [], 'nisn' => []];
                try {
                    $firstLine = fgets($stream);
                    $delimiter = str_contains($firstLine ?: '', ';') ? ';' : ',';
                    rewind($stream);
                    $header = fgetcsv($stream, null, $delimiter, '"', '');
                    $header = array_map(fn (?string $value) => strtolower(trim(ltrim($value ?? '', "\xEF\xBB\xBF"))), $header ?: []);
                    if ($header !== ['nama', 'nis', 'nisn']) {
                        throw ValidationException::withMessages(['file' => 'Judul kolom harus berurutan: nama, nis, nisn. Gunakan template yang tersedia.']);
                    }
                    $line = 1;
                    while (($row = fgetcsv($stream, null, $delimiter, '"', '')) !== false) {
                        $line++;
                        if ($row === [null]) {
                            continue;
                        }
                        if ($line > 501) {
                            throw ValidationException::withMessages(['file' => 'Maksimal 500 baris murid per impor. Pisahkan berkas menjadi beberapa bagian.']);
                        }
                        if (count($row) !== 3) {
                            $errors['file.'.$line] = 'Baris '.$line.': harus berisi tiga kolom (nama, nis, nisn).';

                            continue;
                        }
                        $values = array_map(fn (?string $value) => ($value === null || trim($value) === '') ? null : trim($value), $row);
                        $data = ['name' => $values[0], 'nis' => $values[1], 'nisn' => $values[2]];
                        $validator = Validator::make($data, SaveStudentRequest::identityRules());
                        if ($validator->fails()) {
                            $errors['file.'.$line] = 'Baris '.$line.': '.implode(' ', $validator->errors()->all());
                        }
                        foreach (['nis', 'nisn'] as $key) {
                            if ($data[$key] !== null) {
                                if (isset($seen[$key][$data[$key]])) {
                                    $errors['file.'.$line.'.'.$key] = 'Baris '.$line.': '.strtoupper($key).' sama dengan baris '.$seen[$key][$data[$key]].'.';
                                }
                                $seen[$key][$data[$key]] = $line;
                            }
                        }
                        $rows[] = $data;
                    }
                } finally {
                    fclose($stream);
                }
                if ($errors) {
                    throw ValidationException::withMessages($errors);
                }
                if (! $rows) {
                    throw ValidationException::withMessages(['file' => 'Berkas belum berisi data murid. Isi setidaknya satu baris.']);
                }
                foreach ($rows as $data) {
                    $this->registry->create($user, array_merge($data, ['classroom_id' => $classroomId, 'starts_on' => $date]));
                }

                return count($rows);
            });
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['file' => 'NIS atau NISN sudah disimpan oleh pengguna lain. Tidak ada baris yang diimpor; periksa berkas dan coba lagi.']);
        }
    }
}
