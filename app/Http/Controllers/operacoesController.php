<?php

namespace App\Http\Controllers;

use App\Models\Account;
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
}
