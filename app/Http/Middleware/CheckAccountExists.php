<?php

namespace App\Http\Middleware;

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
    public function handle(Request $request, Closure $next): Response
    {
        $idConta = $request->route('idConta');
        $moeda = $request->route('moeda');
        $moeda = strtoupper($moeda);

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
        ];

        if (!is_numeric($idConta) || !Account::find($idConta)) {
            return response()->json(['error' => 'Conta não existente ou o id da conta é inválido. (O id deve ser do tipo número)'], 400);
        }
        if (!in_array($moeda, $moedasExistentes)) { //! Verifica se a moeda está presente no array $moedaExistente
            return response()->json(['Erro:' => 'A tipo de moeda solicitada não existe.'], 500);
        }


        return $next($request);
    }
}
