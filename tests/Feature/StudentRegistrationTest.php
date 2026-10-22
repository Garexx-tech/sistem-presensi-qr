<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_and_is_logged_in_with_student_role(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Siswa Baru',
            'email' => 'siswa@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $student = User::where('email', 'siswa@example.com')->firstOrFail();

        $response->assertRedirect(route('siswa.home'));
        $this->assertAuthenticatedAs($student);
        $this->assertSame(2, (int) $student->getRawOriginal('role'));
        $this->assertSame('active', $student->status);
        $this->assertNull($student->kelas_id);
        $this->assertMatchesRegularExpression('/^\d{8,}$/', $student->nis);
        $this->assertTrue(Hash::check('rahasia123', $student->password));

        $this->post(route('logout'));
        $this->post(route('login'), [
            'login' => 'siswa@example.com',
            'password' => 'rahasia123',
        ])->assertRedirect(route('siswa.home'));
    }

    public function test_student_nis_is_sequential_for_the_same_date(): void
    {
        Carbon::setTestNow('2026-10-22 10:00:00');

        try {
            foreach ([1, 2] as $number) {
                $this->post(route('register.store'), [
                    'name' => "Siswa Baru {$number}",
                    'email' => "siswa{$number}@example.com",
                    'password' => 'rahasia123',
                    'password_confirmation' => 'rahasia123',
                ])->assertRedirect(route('siswa.home'));
            }

            $this->assertSame('26102201', User::where('email', 'siswa1@example.com')->value('nis'));
            $this->assertSame('26102202', User::where('email', 'siswa2@example.com')->value('nis'));
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_student_cannot_register_with_an_existing_email(): void
    {
        User::create([
            'name' => 'Siswa Terdaftar',
            'email' => 'siswa@example.com',
            'nis' => '123456',
            'password' => 'rahasia123',
            'role' => 2,
            'status' => 'active',
        ]);

        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Pendaftar Baru',
            'email' => 'siswa@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(1, User::count());
    }
}
