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

    public function buscarSaldo($moeda, $idConta)
    {
        $query = $this->newQuery()->where('account_id', $idConta);

        if ($moeda !== null) {
            $query->where('moeda', $moeda);
        }

        return $query->sum('valor');
    }

    use HasFactory;
}
