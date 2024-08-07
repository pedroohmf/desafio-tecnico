<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TaxaCambio
{
    public function getTaxaCambio($moeda)
    {
        $dataCotacao = date('m-d-Y', strtotime('-1 days'));

        $resposta = Http::get("https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?@moeda='" . $moeda . "'&@dataCotacao='06-21-2024'&\$top=100&\$skip=4&\$format=json&\$select=cotacaoCompra,cotacaoVenda");

        if ($resposta->successful()) {
            $resultado = $resposta->json();

            if (!empty($resultado['value'])) {
                $cotacaoCompra = $resultado['value'][0]['cotacaoCompra'];
                $cotacaoVenda = $resultado['value'][0]['cotacaoVenda'];

                return [
                    'cotacaoCompra' => $cotacaoCompra,
                    'cotacaoVenda' => $cotacaoVenda,
                ];
            }
        } else {
            return response()->json(['Erro:' => 'Nenhum dado encontrado para a moeda ' . $moeda . '.'], 400);
        }
    }
}
