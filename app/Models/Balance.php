<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'account_id',
        'moeda',
        'valor'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    use HasFactory;
}
