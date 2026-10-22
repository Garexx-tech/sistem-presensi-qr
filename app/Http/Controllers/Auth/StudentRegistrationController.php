<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentRegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nis' => $this->generateNis(),
            'password' => $validated['password'],
            'role' => 2,
            'status' => 'active',
        ]);

        Auth::login($student);
        $request->session()->regenerate();

        return redirect()->route('siswa.home');
    }

    private function generateNis(): string
    {
        $datePrefix = Carbon::now()->format('ymd');

        $lastSequence = User::query()
            ->where('nis', 'like', $datePrefix . '%')
            ->pluck('nis')
            ->map(fn (string $nis) => substr($nis, strlen($datePrefix)))
            ->filter(fn (string $sequence) => $sequence !== '' && ctype_digit($sequence))
            ->map(fn (string $sequence) => (int) $sequence)
            ->max() ?? 0;

        do {
            $sequence = str_pad((string) ++$lastSequence, 2, '0', STR_PAD_LEFT);
            $nis = $datePrefix . $sequence;
        } while (User::where('nis', $nis)->exists());

        return $nis;
    }
}
