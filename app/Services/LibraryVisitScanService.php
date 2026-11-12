<?php

namespace App\Services;

use App\Models\LibraryAttendanceLog;
use App\Models\LibraryEmployee;
use App\Models\LibraryStudent;
use Carbon\Carbon;

class LibraryVisitScanService
{
    /**
     * Resolve a Library patron.
     *
     * The attendance kiosk matches the stored `qrcode` only. The library
     * scanner also accepts an ID number. Name-normalized USM QR payloads
     * are not used.
     *
     * @return array{student: ?LibraryStudent, employee: ?LibraryEmployee}
     */
    public function resolve(string $rawToken, bool $qrcodeOnly = false): array
    {
        $token = trim(str_replace("\r", '', $rawToken));

        $student = LibraryStudent::query()
            ->where(function ($query) use ($token, $qrcodeOnly) {
                $query->where('qrcode', $token);

                if (! $qrcodeOnly) {
                    $query->orWhere('id_number', $token);
                }
            })
            ->first();

        $employee = $student ? null : LibraryEmployee::query()
            ->where(function ($query) use ($token, $qrcodeOnly) {
                $query->where('qrcode', $token);

                if (! $qrcodeOnly) {
                    $query->orWhere('employee_id', $token);
                }
            })
            ->first();

        return [
            'student' => $student,
            'employee' => $employee,
        ];
    }

    /**
     * Record an IN/OUT library visit for the resolved patron.
     *
     * @return array{status: string, log: LibraryAttendanceLog, student: ?LibraryStudent, employee: ?LibraryEmployee}
     */
    public function record(?LibraryStudent $student, ?LibraryEmployee $employee, ?string $section = null): array
    {
        if (! $student && ! $employee) {
            throw new \InvalidArgumentException('A library student or employee is required.');
        }

        $lastLog = LibraryAttendanceLog::query()
            ->when($student, fn ($query) => $query->where('student_id', $student->id))
            ->when($employee, fn ($query) => $query->where('employee_id', $employee->id))
            ->latest('scanned_at')
            ->latest('id')
            ->first();

        $status = $lastLog && strtoupper((string) $lastLog->status) === 'IN' ? 'OUT' : 'IN';

        $log = LibraryAttendanceLog::query()->create([
            'student_id' => $student?->id,
            'employee_id' => $employee?->id,
            'status' => $status,
            'section' => $section,
            'scanned_at' => Carbon::now('Asia/Manila'),
        ]);

        return [
            'status' => $status,
            'log' => $log,
            'student' => $student,
            'employee' => $employee,
        ];
    }
}
