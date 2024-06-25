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
        // $moeda = strtoupper($moeda);
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

        if ($moeda === null) {
            $saldos = [];
            foreach ($moedasExistentes as $moeda) {
                $saldoMoeda = $saldo->buscarSaldo($moeda, $idConta);
                if ($saldoMoeda > 0) { // Retornando apenas saldos maiores que 0
                    $saldos[$moeda] = $saldoMoeda;
                }
            }

            return response()->json(['saldos' => $saldos]);
        } else {
            //? Retornar o saldo da moeda do parametro
            //! INICIO
            foreach ($moedasExistentes as $moedaExistente) {
                $saldoMoeda = $saldo->buscarSaldo($moedaExistente, $idConta);
                if ($saldoMoeda > 0) {
                    if ($moedaExistente === $moeda) {
                        $saldoTotal += $saldoMoeda;
                    } else {
                        $taxa = $taxaCambio->getTaxaCambio($moeda);
                        $valoresTaxa = $taxa->getContent();
                        $data = json_decode($valoresTaxa, true);
                        if ($taxa) {
                            $cotacaoCompra = $data['cotacaoCompra'];
                            $saldoConvertido = $saldoMoeda * $cotacaoCompra;
                            $saldoTotal += $saldoConvertido;
                        }
                    }
                }
            }
            return response()->json([
                'saldoTotal' => $saldoTotal,
                'moeda' => $moeda
            ]);

            //! FIM
            // if (!in_array($moeda, $moedasExistentes)) {
            //     return response()->json(['Erro:' => 'A tipo de moeda solicitada não existe.'], 500);
            // }

            // $novoSaldo = $saldo->buscarSaldo($moeda, $idConta);
        }

        // return response()->json([
        //     "Sucesso!" => '$novoSaldo',
        //     // 'COTACAO' => $taxaCambio->getTaxaCambio($moeda)
        // ]);
    }
}
