<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;

class AdminController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Redirect berdasarkan role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'siswa' => redirect()->route('siswa.page'),
                'bank' => redirect()->route('bank.page'),
                default => redirect()->route('admin.login')->withErrors(['email' => 'Role tidak dikenali'])
            };
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Anda berhasil logout.');
    }

    // Menampilkan halaman registrasi
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    // Proses registrasi
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,siswa,bank',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Menampilkan dashboard admin
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki akses.');
        }

        return view('admin.dashboard', [
            'admin' => Auth::user(),
            'adminUsers' => User::where('role', 'admin')->get(),
            'siswaUsers' => User::where('role', 'siswa')->get(),
            'bankUsers' => User::where('role', 'bank')->get(),
        ]);
    }

    // Menambahkan user baru
    public function addRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,siswa,bank',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil ditambahkan!');
    }

    // Menampilkan halaman edit user
    public function edit($id)
    {
        return view('admin.edit', ['user' => User::findOrFail($id)]);
    }

    // Memperbarui user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,siswa,bank',
            'password' => 'nullable|min:6',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil diperbarui!');
    }

    // Menghapus user
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'User berhasil dihapus!');
    }

    // Halaman siswa
    public function siswaPage()
    {
        if (Auth::check() && Auth::user()->role === 'siswa') {
            $user = Auth::user();
    
            // Cek apakah wallet sudah ada, jika tidak buat baru
            if (!$user->wallet) {
                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0,
                ]);
            }
    
            // Ambil daftar siswa selain user yang sedang login
            $siswa = User::where('role', 'siswa')
                         ->where('id', '!=', $user->id)
                         ->get();
    
            return view('admin.siswa', compact('user', 'siswa')); 
        }
        
        return redirect()->route('admin.login')->with('error', 'Akses ditolak');
    }

    // Halaman bank
    public function bankPage()
    {
        return Auth::check() && Auth::user()->role === 'bank'
            ? view('admin.bank')
            : redirect()->route('admin.login')->with('error', 'Akses ditolak');
    }

    public function addStudent(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'siswa', // Pastikan role sesuai dengan yang digunakan dalam sistem
    ]);

    return redirect()->back()->with('success', 'Akun siswa berhasil ditambahkan.');
}

}
