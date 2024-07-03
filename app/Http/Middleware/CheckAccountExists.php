<?php

namespace App\Http\Middleware;

use App\Http\Requests\ValidarReqAPI;
use App\Models\Account;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(ValidarReqAPI $req, Closure $next): Response
    {
        $dadosValidados = $req->validated();

        $idConta = $dadosValidados['idConta'];
        $moeda = $dadosValidados['moeda'];
        $valor = $dadosValidados['valor'];

        $moeda = strtoupper($moeda);

        $nomeDaRota = $req->route()->getName();

        $idConta = intval($idConta);

        $moedasExistentes = [
            'AUD',
            'CAD',
            'CHF',
            'DKK',
            'EUR',
            'GBP',
            'JPY',
            'NOK',
            'SEK',
            'USD',
            'BRL'
        ];

        if (!is_numeric($idConta) && !Account::find($idConta)) {
            return response()->json(['error' => 'Conta não existente ou o id da conta é inválido. (O id deve ser do tipo número)'], 400);
        }

        if (strlen($moeda) !== 3 && $nomeDaRota !== 'saldo') {
            return response()->json(['error' => "A moeda tem que ter 3 letras. Ex: (USD, EUR, BRL...)"], 400);
        }

        if (!is_numeric($valor) && $valor > 0.1 && $nomeDaRota !== 'saldo') {
            return response()->json(['error' => 'O valor do saque deve ser um número e também deve ser maior que 0.1.'], 400);
        }

        if ($nomeDaRota !== 'saldo') {
            if (!in_array($moeda, $moedasExistentes)) { //! Verifica se a moeda está presente no array $moedaExistente
                return response()->json(['Erro:' => 'A tipo de moeda solicitada não existe.'], 400);
            }
        }


        return $next($req);
    }
}
