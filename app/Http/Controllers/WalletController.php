<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\TopUpRequest;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    // Menampilkan dashboard siswa dengan saldo dan transaksi
    public function showSiswaDashboard()
    {
        $siswa = User::where('role', 'siswa')
                     ->where('id', '!=', Auth::id())
                     ->get();

        $transactions = Transaction::where('sender_id', Auth::id())
                        ->orWhere('receiver_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('siswa', compact('siswa', 'transactions'));
    }

    // Transfer saldo antar siswa
    public function transferSaldo(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $sender = Auth::user();
        $receiver = User::find($request->receiver_id);

        if (!$receiver || $receiver->role !== 'siswa') {
            return back()->with('error', 'Penerima harus sesama siswa.');
        }

        $senderWallet = Wallet::firstOrCreate(['user_id' => $sender->id]);
        $receiverWallet = Wallet::firstOrCreate(['user_id' => $receiver->id]);

        if ($senderWallet->balance < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        // Kurangi saldo pengirim
        $senderWallet->balance -= $request->amount;
        $senderWallet->save();

        // Tambah saldo penerima
        $receiverWallet->balance += $request->amount;
        $receiverWallet->save();

        // Catat transaksi
        Transaction::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => $request->amount,
            'type' => 'transfer'
        ]);

        return back()->with('success', 'Transfer berhasil.');
    }

    // Ajukan top-up saldo
    public function requestTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        TopUpRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Permintaan top-up berhasil diajukan.');
    }

    // Konfirmasi top-up oleh admin/bank
    public function confirmTopUp($id)
    {
        $topUp = TopUpRequest::findOrFail($id);
    
        if ($topUp->status == 'pending') {
            $wallet = Wallet::firstOrCreate(['user_id' => $topUp->user_id]);
            $wallet->balance += $topUp->amount;
            $wallet->save();
    
            $topUp->status = 'approved';
            $topUp->save();
    
            // Catat transaksi top-up dengan sender_id = NULL
            Transaction::create([
                'sender_id' => null, // FIX: Jangan pakai 0, harus NULL
                'receiver_id' => $topUp->user_id,
                'amount' => $topUp->amount,
                'type' => 'top-up'
            ]);
    
            return redirect()->back()->with('success', 'Top-up berhasil dikonfirmasi.');
        }
    
        return redirect()->back()->with('error', 'Top-up sudah diproses sebelumnya.');
    }
    

}
