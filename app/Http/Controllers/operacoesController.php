<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Balance;
use Illuminate\Http\Request;

class operacoesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function create()
    {
        $novaConta = new Account();
        $novaConta->save();
        $teste = Account::latest('id')->first()->id;

        return response()->json(['Conta criada!' => "O id da conta é: " . $teste], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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

    public function saldo(Request $req, $moeda = null)
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
        ];

        if ($moeda === null) {
            // Retornar saldo de cada tipo de moeda existente

        } else {
            // Retornar o saldo da moeda do parametro
            if (!in_array($moeda, $moedasExistentes)) {
                return response()->json(['Erro:' => 'A tipo de moeda solicitada não existe.'], 500);
            }

            $novoSaldo = $saldo->buscarSaldo($moeda, $idConta);
        }

        return response()->json(["Sucesso!" => $novoSaldo]);



        // $saldo = new Balance();

        // $saldos = [];
        // if ($moeda === null) {
        //     foreach ($moedasExistentes as $moeda) {
        //         $saldo = (new Balance())->buscarSaldo($id, $moeda);
        //         $saldos = ['moeda' => $moeda, 'valor' => $saldo];
        //     }

        //     $saldoTotal = $saldo->buscarSaldo($moeda, $id);
        // } else {
        //     $saldoTotal = $saldo->buscarSaldo($moeda, $id);
        // }
        // return response()->json(["Sucesso!" => $saldos]);
    }
}
