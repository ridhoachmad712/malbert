<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Signature('school:backup {--restore= : Restore a backup into an empty application database}')]
#[Description('Back up school records and local media, or restore into an empty database')]
class SchoolBackup extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = Storage::disk('local');
        $tables = ['users', 'school_settings', 'activity_logs', 'academic_years', 'classrooms', 'classroom_user', 'students', 'enrollments', 'reading_resources', 'literacy_assignments', 'literacy_submissions', 'publications', 'literacy_reviews', 'assessment_settings', 'literacy_assessments', 'literacy_assessment_revisions', 'point_transactions'];
        if ($source = $this->option('restore')) {
            foreach ($tables as $table) {
                if (DB::table($table)->exists()) {
                    $this->error('Restore requires an empty application database. Existing records were not changed.');

                    return self::FAILURE;
                }
            }
            $zip = new \ZipArchive;
            if ($zip->open($source) !== true) {
                $this->error('Cannot open backup.');

                return self::FAILURE;
            }
            $payload = json_decode($zip->getFromName('records.json') ?: '', true);
            if (! in_array($payload['version'] ?? null, [1, 2, 3, 4, 5, 6], true) || ! isset($payload['tables'])) {
                $zip->close();
                $this->error('Invalid backup format.');

                return self::FAILURE;
            }
            $files = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if ($name === 'records.json') {
                    continue;
                }
                if (! preg_match('~^(?:media/[a-zA-Z0-9-]+\.webp|literacy/submissions/[a-f0-9-]+\.(?:pdf|jpe?g|png|webp)|publication-assets/[a-f0-9-]+\.(?:pdf|jpe?g|png|webp))$~i', $name) || $disk->exists($name)) {
                    $zip->close();
                    $this->error('Unsafe or conflicting media path. No records restored.');

                    return self::FAILURE;
                }
                $files[] = $name;
            }
            $written = [];
            try {
                DB::transaction(function () use ($tables, $payload, $files, $zip, $disk, &$written) {
                    foreach ($tables as $table) {
                        foreach (array_chunk($payload['tables'][$table] ?? [], 100) as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                    foreach ($files as $name) {
                        if (! $disk->put($name, $zip->getFromName($name))) {
                            throw new \RuntimeException('Media restore failed.');
                        }
                        $written[] = $name;
                    }
                });
            } catch (\Throwable $exception) {
                $disk->delete($written);
                throw $exception;
            } finally {
                $zip->close();
            }
            $this->info('Records and local media restored. Drive originals remain in Google Drive.');

            return self::SUCCESS;
        }
        $records = DB::transaction(function () use ($tables) {
            $records = [];
            foreach ($tables as $table) {
                $records[$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            }

            return $records;
        });
        $disk->makeDirectory('backups');
        $path = 'backups/school-'.now()->format('Ymd-His').'-'.Str::random(6).'.zip';
        $zip = new \ZipArchive;
        if ($zip->open($disk->path($path), \ZipArchive::CREATE) !== true) {
            $this->error('Cannot create backup.');

            return self::FAILURE;
        }
        $zip->addFromString('records.json', json_encode(['version' => 6, 'created_at' => now()->toIso8601String(), 'tables' => $records], JSON_THROW_ON_ERROR));
        foreach ($disk->files('media') as $file) {
            $zip->addFile($disk->path($file), $file);
        }
        foreach ($disk->allFiles('literacy') as $file) {
            $zip->addFile($disk->path($file), $file);
        }
        foreach ($disk->allFiles('publication-assets') as $file) {
            $zip->addFile($disk->path($file), $file);
        }
        if (! $zip->close()) {
            $this->error('Backup could not be completed.');

            return self::FAILURE;
        }
        foreach ($disk->files('backups') as $old) {
            if (preg_match('~^backups/school-[\w-]+\.zip$~', $old) && $disk->lastModified($old) < now()->subDays(14)->timestamp) {
                $disk->delete($old);
            }
        }
        $this->info('Backup saved: '.$disk->path($path));

        return self::SUCCESS;
    }
}
