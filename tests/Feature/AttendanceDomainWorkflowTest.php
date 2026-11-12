<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AttendanceLog;
use App\Models\AttendanceStudent;
use App\Models\Book;
use App\Models\BookLog;
use App\Models\Employee;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AttendanceDomainWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_official_kiosk_scan_uses_library_student_records(): void
    {
        $student = Student::query()->create([
            'id_number' => '25-00003',
            'firstname' => 'Dorothy',
            'lastname' => 'Vaughan',
            'qrcode' => 'LIB-QR-0003',
        ]);

        $response = $this->postJson('/attendance', [
            'qrcode' => 'LIB-QR-0003',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('type', 'student')
            ->assertJsonPath('student_id', $student->id)
            ->assertJsonPath('status', 'IN');

        $this->assertDatabaseHas('library_attendance_logs', [
            'student_id' => $student->id,
            'status' => 'IN',
        ]);
        $this->assertDatabaseCount('attendance_logs', 0);
    }

    public function test_official_kiosk_scan_succeeds_when_sms_modem_is_down(): void
    {
        Http::fake(function () {
            throw new ConnectionException('modem down');
        });

        $student = Student::query()->create([
            'id_number' => '25-00008',
            'firstname' => 'Ada',
            'lastname' => 'Lovelace',
            'qrcode' => 'S-SMS-0008',
            'mobile_number' => '09171234567',
        ]);

        $this->postJson('/attendance', [
            'qrcode' => 'S-SMS-0008',
        ])
            ->assertOk()
            ->assertJsonPath('type', 'student')
            ->assertJsonPath('student_id', $student->id)
            ->assertJsonPath('status', 'IN');

        $this->assertDatabaseHas('library_attendance_logs', [
            'student_id' => $student->id,
            'status' => 'IN',
        ]);
    }

    public function test_official_kiosk_scan_uses_student_qrcode_not_id_or_normalized_name(): void
    {
        $student = Student::query()->create([
            'id_number' => '24-19462',
            'firstname' => 'Fatma',
            'lastname' => 'Aba',
            'qrcode' => 'S-LIB-19462',
        ]);
        $student->forceFill(['normalized_name' => 'FATMAABA'])->save();

        $this->postJson('/attendance', [
            'qrcode' => '24-19462',
        ])
            ->assertOk()
            ->assertJsonPath('type', 'error');

        $this->postJson('/attendance', [
            'qrcode' => "24-19462\nFatma Aba",
        ])
            ->assertOk()
            ->assertJsonPath('type', 'error');

        $this->postJson('/attendance', [
            'qrcode' => 'FATMAABA',
        ])
            ->assertOk()
            ->assertJsonPath('type', 'error');

        $this->postJson('/attendance', [
            'qrcode' => 'S-LIB-19462',
        ])
            ->assertOk()
            ->assertJsonPath('type', 'student')
            ->assertJsonPath('student_id', $student->id)
            ->assertJsonPath('status', 'IN');

        $this->assertDatabaseCount('library_attendance_logs', 1);
    }

    public function test_official_kiosk_scan_uses_employee_qrcode(): void
    {
        $employee = Employee::query()->create([
            'employee_id' => 'EMP-0007',
            'firstname' => 'Grace',
            'lastname' => 'Hopper',
            'qrcode' => 'E-LIB-0007',
        ]);
        $employee->forceFill(['normalized_name' => 'GRACEHOPPER'])->save();

        $this->postJson('/attendance', [
            'qrcode' => 'EMP-0007',
        ])
            ->assertOk()
            ->assertJsonPath('type', 'error');

        $this->postJson('/attendance', [
            'qrcode' => 'E-LIB-0007',
        ])
            ->assertOk()
            ->assertJsonPath('type', 'employee')
            ->assertJsonPath('employee_id', $employee->id)
            ->assertJsonPath('status', 'IN');

        $this->assertDatabaseHas('library_attendance_logs', [
            'employee_id' => $employee->id,
            'status' => 'IN',
        ]);
    }

    public function test_official_kiosk_scan_rejects_attendance_only_patron(): void
    {
        AttendanceStudent::query()->create([
            'student_id' => 'ATT-001',
            'firstname' => 'Grace',
            'lastname' => 'Hopper',
            'qrcode' => 'ATT-QR-001',
        ]);

        $this->postJson('/attendance', [
            'qrcode' => 'ATT-QR-001',
        ])
            ->assertOk()
            ->assertJsonPath('type', 'error')
            ->assertJsonPath('message', 'Not Recognized');

        $this->assertDatabaseCount('library_attendance_logs', 0);
        $this->assertDatabaseCount('attendance_logs', 0);
    }

    public function test_official_kiosk_alarms_when_book_rfid_is_not_checked_out(): void
    {
        $onShelf = Book::query()->create([
            'title_statement' => 'On the shelf',
            'rfid' => 'RFID-SHELF',
            'barcode' => 'BC-SHELF',
            'accession_no' => 'ACC-SHELF',
        ]);
        $returned = Book::query()->create([
            'title_statement' => 'Already returned',
            'rfid' => 'RFID-RETURNED',
        ]);
        BookLog::query()->create([
            'book_id' => $returned->id,
            'status' => 'Checked In',
            'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
        ]);
        $borrowed = Book::query()->create([
            'title_statement' => 'Borrowed copy',
            'rfid' => 'RFID-OUT',
        ]);
        BookLog::query()->create([
            'book_id' => $borrowed->id,
            'status' => 'Checked Out',
            'circulation_type' => BookLog::CIRCULATION_CHECKOUT,
        ]);

        $this->postJson('/attendance', ['qrcode' => 'RFID-SHELF'])
            ->assertOk()
            ->assertJsonPath('type', 'book')
            ->assertJsonPath('bookStatus', 'Not Checked Out')
            ->assertJsonPath('book.title_statement', 'On the shelf');

        $this->postJson('/attendance', ['qrcode' => 'RFID-RETURNED'])
            ->assertOk()
            ->assertJsonPath('type', 'book')
            ->assertJsonPath('bookStatus', 'Not Checked Out')
            ->assertJsonPath('book.title_statement', 'Already returned');

        $this->postJson('/attendance', ['qrcode' => 'RFID-OUT'])
            ->assertOk()
            ->assertJsonPath('type', 'book')
            ->assertJsonPath('bookStatus', 'Checked Out')
            ->assertJsonPath('book.title_statement', 'Borrowed copy');

        $this->postJson('/attendance', ['qrcode' => 'BC-SHELF'])
            ->assertOk()
            ->assertJsonPath('type', 'error');

        $this->postJson('/attendance', ['qrcode' => 'ACC-SHELF'])
            ->assertOk()
            ->assertJsonPath('type', 'error');

        $this->assertDatabaseCount('library_attendance_logs', 0);
        $this->assertSame($onShelf->id, Book::query()->where('rfid', 'RFID-SHELF')->value('id'));
    }

    public function test_attendance_admin_can_view_daily_student_absences(): void
    {
        $admin = $this->attendanceAdmin();
        $present = AttendanceStudent::query()->create([
            'student_id' => '25-00004',
            'firstname' => 'Mary',
            'lastname' => 'Jackson',
            'course' => 'BSIT',
            'year' => 'First Year',
        ]);
        $absent = AttendanceStudent::query()->create([
            'student_id' => '25-00005',
            'firstname' => 'Katherine',
            'lastname' => 'Johnson',
            'course' => 'BSIT',
            'year' => 'First Year',
        ]);

        AttendanceLog::query()->create([
            'student_id' => $present->id,
            'status' => 'IN',
            'scanned_at' => '2026-07-09 07:30:00',
        ]);

        $this->actingAs($admin)
            ->get('/attendance-logs/absences?date=2026-07-09')
            ->assertOk()
            ->assertSee('Attendance Absences')
            ->assertSee($absent->lastname)
            ->assertDontSee($present->lastname);
    }

    public function test_attendance_absences_are_admin_only(): void
    {
        $staff = User::factory()->create([
            'role' => 'attendance_staff',
            'is_active' => true,
        ]);
        $staff->assignRole('attendance_staff');

        $this->actingAs($staff)
            ->get('/attendance-logs/absences')
            ->assertForbidden();
    }

    private function attendanceAdmin(): User
    {
        $admin = User::factory()->create([
            'role' => 'attendance_admin',
            'is_active' => true,
        ]);
        $admin->assignRole('attendance_admin');

        return $admin;
    }
}
