<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createTopUp($user_id, $amount)
    {
        return self::create([
            'user_id' => $user_id,
            'type' => 'topup',
            'amount' => $amount,
            'status' => 'pending',
        ]);
    }
    

}

