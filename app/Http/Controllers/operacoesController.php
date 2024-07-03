<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidarReqAPI;
use App\Models\Account;
use App\Models\Balance;
use Illuminate\Http\Request;
use App\Services\TaxaCambio;
use Mockery\Undefined;
use App\Services\BuscarMoedas;

class operacoesController extends Controller
{
    public function create()
    {
        $novaConta = new Account();
        $novaConta->save();
        $conta = Account::latest('id')->first()->id;

        return response()->json(['Conta criada!' => "O id da conta é: " . $conta], 200);
    }

    public function deposito(ValidarReqAPI $req, BuscarMoedas $buscarMoedas)
    {
        $dadosValidados = $req->validated();

        $idConta = $dadosValidados['idConta'];
        $valor = $dadosValidados['valor'];


        $response = $buscarMoedas->getMoedas();
        $moedas = $response->getData();

        if (!empty($moedas) && in_array($dadosValidados['moeda'], $moedas)) {
            $moeda = $dadosValidados['moeda'];
        } else {
            return response()->json(['error' => 'Moeda não existente.'], 404);
        }

        $conta = Account::find($idConta);

        if (!$conta) {
            return response()->json(['error' => "Conta não encontrada."], 400);
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

        return response()->json(['message' => 'Depósito realizado com sucesso.'], 201);
    }

    public function saldo(ValidarReqAPI $req)
    {
        $dadosValidados = $req->validated();

        $idConta = $dadosValidados['idConta'];
        $moeda = $dadosValidados['moeda'] ?? null;

        $saldos = Balance::where('account_id', $idConta)->get();

        $response = $saldos->map(function ($saldos) {
            return [
                'moeda' => $saldos->moeda,
                'valor' => $saldos->valor
            ];
        });

        $saldoMoedaParam = Balance::where('account_id', $idConta)
            ->where('moeda', $moeda)
            ->first();

        if ($moeda === null) {
            return response()->json($response);
        } else {
            if ($saldoMoedaParam) {
                return response()->json([$saldoMoedaParam->valor], 200);
            } else {
                return response()->json(['erro' => 'Saldo não encontrado para a moeda especificada.'], 404);
            }
        }
    }

    public function saque(ValidarReqAPI $req, BuscarMoedas $buscarMoedas)
    {
        $dadosValidados = $req->validated();

        $idConta = $dadosValidados['idConta'];
        $valor = floatval($dadosValidados['valor']);
        $response = $buscarMoedas->getMoedas();
        $moedas = $response->getData();

        if (!empty($moedas) && in_array($dadosValidados['moeda'], $moedas)) {
            $moeda = $dadosValidados['moeda'];
        } else {
            return response()->json(['error' => 'Moeda não existente.'], 404);
        }

        $saldos = Balance::where('account_id', $idConta)->get();

        $response = $saldos->map(function ($saldos) {
            return [
                'moeda' => $saldos->moeda,
                'valor' => $saldos->valor
            ];
        });


        $saldo = new Balance();
        $saldo = Balance::where('account_id', $idConta)
            ->where('moeda', floatval($moeda))
            ->first();;

        if ($saldo < $valor) {
            $taxa = new TaxaCambio();
            $cotacaoMoedaParam = $taxa->getTaxaCambio($moeda);
            $dadosMoedas = [];
            $somaValoresConvertidos = 0.0;

            foreach ($response as $moedaSaldo) {
                if ($moedaSaldo['moeda'] !== $moeda && $moedaSaldo['moeda'] !== 'BRL') {
                    $taxaCambioMoedasSaldo = $taxa->getTaxaCambio($moedaSaldo['moeda']);
                    if ($taxaCambioMoedasSaldo !== null) {
                        $novoItem = [
                            'moeda' => $moedaSaldo['moeda'],
                            'valor' => $moedaSaldo['valor'],
                            'cotacaoCompra' => $taxaCambioMoedasSaldo['cotacaoCompra'],
                            'cotacaoVenda' => $taxaCambioMoedasSaldo['cotacaoVenda'],
                            'valorConvertido' => round($moedaSaldo['valor'] * $taxaCambioMoedasSaldo['cotacaoCompra'] / $cotacaoMoedaParam['cotacaoVenda'], 2),
                        ];
                        $dadosMoedas[] = $novoItem;
                        $somaValoresConvertidos += round($novoItem['valorConvertido'], 2);
                    }
                } else {
                    $taxaCambioMoedasSaldo = $taxa->getTaxaCambio($moedaSaldo['moeda']);
                    if ($moedaSaldo['moeda'] === $moeda) {
                        $novoItem = [
                            'moeda' => $moedaSaldo['moeda'],
                            'valor' => $moedaSaldo['valor'],
                            'cotacaoCompra' => $taxaCambioMoedasSaldo['cotacaoCompra'] ?? null,
                            'cotacaoVenda' => $taxaCambioMoedasSaldo['cotacaoVenda'] ?? null,
                            'valorConvertido' => round($moedaSaldo['valor']),
                        ];
                        $dadosMoedas[] = $novoItem;
                        $somaValoresConvertidos += round($novoItem['valorConvertido'], 2);
                    } elseif ($moedaSaldo['moeda'] === 'BRL') {
                        $novoItem = [
                            'moeda' => $moedaSaldo['moeda'],
                            'valor' => $moedaSaldo['valor'],
                            'cotacaoCompra' => $taxaCambioMoedasSaldo['cotacaoCompra'] ?? null,
                            'cotacaoVenda' => $taxaCambioMoedasSaldo['cotacaoVenda'] ?? null,
                            'valorConvertido' => round($moedaSaldo['valor'] / $cotacaoMoedaParam['cotacaoVenda'], 2),
                        ];
                        $dadosMoedas[] = $novoItem;
                        $somaValoresConvertidos += $novoItem['valorConvertido'];
                    }
                }
            }
            // return response()->json([
            //     'Dados das moedas + saldos' => $dadosMoedas,
            //     'Soma dos valores convertidos' => $somaValoresConvertidos,
            // ], 200);

            if ($somaValoresConvertidos > $valor) {
                $saque = $somaValoresConvertidos - $valor;

                return response()->json(["sucess" => "Agora o seu saldo é $saque " . $moeda], 200);
            } else {
                return response()->json(["error" => "Saldo insuficiente."], 400);
            }
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


    public function dados(Request $req)
    {
        $idConta = $req->route('idConta');
        $account = Account::find($idConta);

        if ($account) {
            $balances = $account->balances;
        } else {
            return response()->json(['error' => 'Conta não encontrada.'], 404);
        }
        return $balances;
    }
}
