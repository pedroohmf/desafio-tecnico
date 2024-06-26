<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Balance;
use Illuminate\Http\Request;
use App\Services\TaxaCambio;
use Mockery\Undefined;

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

            foreach ($moedasExistentes as $moedaElemento) {
                $saldoMoeda = $saldo->buscarSaldo($moedaElemento, $idConta);
                if ($saldoMoeda > 0) {
                    $saldoMoedas[$moedaElemento] = $saldoMoeda;
                }
            }

            $siglasMoedasComSaldo = array_keys($saldoMoedas);
            $taxas = new TaxaCambio();
            $cotacaoVendaMoedaParam = $taxas->getTaxaCambio($moeda);

            $taxasCambio = [];
            foreach ($siglasMoedasComSaldo as $siglaMoeda) {
                if ($siglaMoeda !== 'BRL') {
                    $taxa = $taxas->getTaxaCambio($siglaMoeda);
                    $taxasCambio[$siglaMoeda] = $taxa['cotacaoCompra'];
                }
            }

            $resultados = [];
            foreach ($saldoMoedas as $moedaSaldo => $saldo) {

                if (isset($taxasCambio[$moedaSaldo])) { //? Verifica se a moeda existe em $taxasCambio
                    $resultados[$moedaSaldo] = round($saldo * $taxasCambio[$moedaSaldo] / $cotacaoVendaMoedaParam['cotacaoVenda']); //? Multiplica o saldo pela taxa de câmbio / contacaoVenda da moeda passada no parametro
                } else {
                    $resultados[$moedaSaldo] = round($saldo / $cotacaoVendaMoedaParam['cotacaoVenda']);
                }
            }

            $somaTotal = array_sum($resultados);
            $somaTotalFormatada = number_format($somaTotal, 2, ',', '.');
            $somaTotalFormatada = rtrim(rtrim($somaTotalFormatada, '0'), ',');

            return response()->json([
                'Saldo referente a cada moeda: ' => $saldoMoedas,
                'cotacaoCompra das moedas com saldo MAIOR que 0 no banco de dados: ' => $taxasCambio,
                'Cotacao da moeda passada por parametro ($moeda)' => $cotacaoVendaMoedaParam,
                'Resultado das conversoes: ' => $resultados,
                'Saldo Total de todas moedas para a moeda ' . $moeda . ":" => $somaTotalFormatada
            ], 200);

            // $saldoMoedas;  //! retora saldo das moedas > 0
            // $cotacaoVendaMoedaParam['cotacaoVenda'];
            // $taxasCambio;    //? cotacaoCompra das moedas com saldo no banco junto com as siglas da moeda (exceto BRL)
        }
    }

    public function saque(Request $req)
    {
        // $req->validate([
        //     'idConta' => 'required|numeric',
        //     'moeda' => 'required|string|max:3',
        //     'valor' => 'required|numeric|min:0.01'
        // ]);

        $idConta = $req->route('idConta');
        $moeda = $req->route('moeda');
        $valor = $req->route('valor');

        $saldo = new Balance();
        $saldo = Balance::where('account_id', $idConta)
            ->where('moeda', $moeda)
            ->first();;

        if ($saldo < $valor) {

            // Caso a conta não possua saldo suficiente para o saque na moeda solicitada, deverá ser
            // realizada a conversão dos saldos das outras moedas para a moeda solicitada da seguinte
            // forma:
            // ◦ Caso o saldo na conta seja em Real, converter com a taxa de venda PTAX para a moeda
            // solicitada no saque;
            // ◦ Caso contrário, converter o saldo na conta primeiro para Real a partir da taxa de compra
            // PTAX, e depois converter o saldo em Real para a moeda solicitada no saque a partir da
            // taxa de venda PTAX

            // if ($moeda === 'BRL') {
            // } else {
            // }
        } else {


            if ($saldo) {
                $saldo->valor -= $valor;
                $saldo->save();
            }
        }

        return response()->json([
            'Moeda desejada: ' => $moeda,
            'Saque realizado com sucesso' => $valor,
            'Agora o seu saldo em ' . $moeda . ' é' => $saldo['valor'],
        ], 200);
    }
}
