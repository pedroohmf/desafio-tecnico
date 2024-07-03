<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BuscarMoedas
{
    public function getMoedas()
    {
        $resposta = Http::get("https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/Moedas?\$top=100&\$format=json&\$select=simbolo");

        if ($resposta->successful()) {
            $resultado = $resposta->json();

            if (!empty($resultado['value'])) {
                $simbolos = $resultado['value'];

                $simbolos = array_map(function ($moeda) {
                    return $moeda['simbolo'];
                }, $simbolos);

                $simbolos[] = "BRL";

                return response()->json($simbolos, 200);
            }

            return response()->json([], 404);
        } else {
            return response()->json(['Erro:' => 'Nenhum dado encontrado para a moeda '], 404);
        }
    }
}
