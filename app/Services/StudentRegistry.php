<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentRegistry
{
    public function classroom(User $user, int $id): Classroom
    {
        return Classroom::visibleTo($user)->with('academicYear')->whereKey($id)->lockForUpdate()->firstOrFail();
    }

    public function validateDate(Classroom $classroom, string $date): void
    {
        if ($date < $classroom->academicYear->starts_on->toDateString() || $date > $classroom->academicYear->ends_on->toDateString()) {
            throw ValidationException::withMessages(['starts_on' => 'Tanggal masuk harus berada dalam rentang tahun ajaran kelas tujuan.']);
        }
    }

    /** @param array{name: string, nis?: ?string, nisn?: ?string, classroom_id: int|string, starts_on: string, active?: bool|int|string, verified?: bool|int|string} $data */
    public function create(User $user, array $data): Student
    {
        return DB::transaction(function () use ($user, $data) {
            $classroom = $this->classroom($user, (int) $data['classroom_id']);
            $this->validateDate($classroom, $data['starts_on']);
            $verified = $user->role === 'admin' && ! empty($data['verified']);
            $student = Student::create([
                'name' => $data['name'], 'nis' => $data['nis'] ?? null, 'nisn' => $data['nisn'] ?? null,
                'classroom_id' => $classroom->id,
                'active' => $user->role === 'admin' ? ($data['active'] ?? true) : true,
                'verified_at' => $verified ? now() : null, 'verified_by' => $verified ? $user->id : null,
            ]);
            $student->enrollments()->create(['classroom_id' => $classroom->id, 'starts_on' => $data['starts_on'], 'recorded_by' => $user->id]);
            ActivityLog::record('Mendaftarkan murid', 'Murid #'.$student->id.' · rombel #'.$classroom->id);

            return $student;
        });
    }

    public function transfer(Student $student, User $user, int $classroomId, string $date): void
    {
        abort_unless($user->role === 'admin', 403);
        DB::transaction(function () use ($student, $user, $classroomId, $date) {
            $student = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();
            $classroom = $this->classroom($user, $classroomId);
            $this->validateDate($classroom, $date);
            $previous = $student->enrollments()->whereNull('ends_on')->firstOrFail();
            if ($student->classroom_id === $classroom->id || $date <= $previous->starts_on->toDateString()) {
                throw ValidationException::withMessages(['classroom_id' => 'Pilih kelas berbeda dengan tanggal pindah setelah tanggal masuk terakhir.']);
            }
            $previous->update(['ends_on' => $date]);
            $student->enrollments()->create(['classroom_id' => $classroom->id, 'starts_on' => $date, 'recorded_by' => $user->id]);
            $student->update(['classroom_id' => $classroom->id]);
            ActivityLog::record('Memindahkan murid', 'Murid #'.$student->id.' · rombel #'.$previous->classroom_id.' → #'.$classroom->id.' · '.$date);
        });
    }
}
