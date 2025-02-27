<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Wallet (Setiap user punya satu wallet)
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }


        /**
     * Relasi untuk semua transaksi (baik yang dikirim maupun diterima)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'sender_id')
                    ->orWhere('receiver_id', $this->id);
    }


    /**
     * Relasi ke Transaction sebagai penerima
     */
    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'receiver_id');
    }

    // Buat wallet otomatis saat user baru dibuat
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->role === 'siswa') { // Hanya buat wallet untuk siswa
                Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0, // Saldo awal 0
                ]);
            }
        });
    }
    
}
