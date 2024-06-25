<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Balance;
use Illuminate\Http\Request;
use App\Services\TaxaCambio;

class operacoesController extends Controller
{
    public function create()
    {
        $novaConta = new Account();
        $novaConta->save();
        $teste = Account::latest('id')->first()->id;

        return response()->json(['Conta criada!' => "O id da conta é: " . $teste], 200);
    }

    public function deposito(Request $req)
    {
        $id = $req->route('idConta');
        $moeda = $req->route('moeda');
        $valor = $req->route('valor');

        $conta = Account::find($id);

        if (!$conta) {
            return response()->json(['error' => "Conta não encontrada."]);
        }

        $balance = $conta->balances()->where('moeda', strtoupper($moeda))->first();

        if ($balance) {
            $balance->update(['valor' => $balance->valor += $valor]);
        } else {
            $conta->balances()->create([
                'moeda' => strtoupper($moeda),
                'valor' => $valor,
            ]);
        }

        return response()->json(['message' => 'Depósito realizado com sucesso.']);
    }

    public function saldo(Request $req, $moeda = null, TaxaCambio $taxaCambio)
    {
        $idConta = $req->route('idConta');
        $moeda = $req->route('moeda', null);

        $saldo = new Balance();

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

        $saldoTotal = 0;

        $saldos = [];

        if ($moeda === null) {
            foreach ($moedasExistentes as $moeda) {
                $saldoMoeda = $saldo->buscarSaldo($moeda, $idConta);
                if ($saldoMoeda > 0) { //? Retornando apenas saldos maiores que 0
                    $saldos[$moeda] = $saldoMoeda;
                }
            }
            return response()->json([$saldos]);
        } else {
            //? Retornar o saldo da moeda do parametro
            $saldoMoeda = $saldo->buscarSaldo($moeda, $idConta);
            $saldoMoedas = [];

            foreach ($moedasExistentes as $moedaTeste) {
                $saldoMoeda = $saldo->buscarSaldo($moedaTeste, $idConta);
                if ($saldoMoeda > 0) {
                    $saldoMoedas[$moedaTeste] = $saldoMoeda;
                }
            }

            $siglasMoedasComSaldo = array_keys($saldoMoedas);
            $taxas = new TaxaCambio();

            $taxasCambio = [];

            foreach ($siglasMoedasComSaldo as $siglaMoeda) {
                $taxa = $taxas->getTaxaCambio($siglaMoeda);
                $taxasCambio[$siglaMoeda] = $taxa;
            }

            $taxasCambio[$siglaMoeda] = $taxas->getTaxaCambio($moeda);
            return $taxasCambio;
        }
    }
}
