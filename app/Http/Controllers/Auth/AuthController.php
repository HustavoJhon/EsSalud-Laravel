<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->locked_until && now()->lt($user->locked_until)) {
            $remaining = now()->diffInMinutes($user->locked_until);
            return back()->withErrors([
                'email' => "Cuenta bloqueada. Intente nuevamente en {$remaining} minutos.",
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->update([
                'failed_login_attempts' => 0,
                'locked_until' => null,
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return redirect()->intended(route('home'));
        }

        if ($user) {
            $attempts = $user->failed_login_attempts + 1;
            $lockedUntil = null;

            if ($attempts >= 5) {
                $lockedUntil = now()->addMinutes(30);
            }

            $user->update([
                'failed_login_attempts' => $attempts,
                'locked_until' => $lockedUntil,
            ]);
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas son incorrectas.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'dni' => 'nullable|string|max:20|unique:users',
            'phone' => 'nullable|string|max:15',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'dni' => $validated['dni'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'full_name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'role' => 'ASEG',
            'password_changed_at' => now(),
        ]);

        $user->assignRole('ASEG');
        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showProfile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'dni' => 'nullable|string|max:20|unique:users,dni,' . Auth::id(),
        ]);

        Auth::user()->update($validated);

        return back()->with('status', 'Perfil actualizado correctamente.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
        ]);

        return back()->with('status', 'Contraseña actualizada correctamente.');
    }

    public function forgotPasswordShow()
    {
        return view('auth.forgot-password');
    }

    public function forgotPasswordSend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordShow(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPasswordUpdate(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'password_changed_at' => now(),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function apiRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'dni' => 'nullable|string|max:20|unique:users',
            'phone' => 'nullable|string|max:15',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'dni' => $validated['dni'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'full_name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'role' => 'ASEG',
            'password_changed_at' => now(),
        ]);

        $user->assignRole('ASEG');
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->locked_until && now()->lt($user->locked_until)) {
            return response()->json([
                'message' => 'Cuenta bloqueada temporalmente.',
            ], 423);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            if ($user) {
                $attempts = $user->failed_login_attempts + 1;
                $lockedUntil = null;
                if ($attempts >= 5) {
                    $lockedUntil = now()->addMinutes(30);
                }
                $user->update([
                    'failed_login_attempts' => $attempts,
                    'locked_until' => $lockedUntil,
                ]);
            }

            return response()->json([
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        $user = Auth::user();
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('roles'),
            'token' => $token,
        ]);
    }

    public function apiRefresh(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }
}
