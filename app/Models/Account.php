<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $fillable = [];

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }


    use HasFactory;
}
