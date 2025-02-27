<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'amount', 'type'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
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
