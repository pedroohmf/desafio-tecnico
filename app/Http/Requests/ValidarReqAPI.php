<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidarReqAPI extends FormRequest
{

    public function rules(): array
    {
        $nomeDaRota = $this->route()->getName();

        // $rules = [

        // ];

        if ($nomeDaRota === 'saldo') {
            return [
                'idConta' => 'required|exists:accounts,id',
                'moeda' => 'nullable|string|size:3',
                'valor' => 'nullable|numeric|min:0.01'
            ];
        }
        return [
            'idConta' => 'required|exists:accounts,id',
            'moeda' => 'required|string|size:3',
            'valor' => 'required|numeric|min:0.01'
        ];
    }

    public function messages(): array
    {
        return [
            'idConta.required' => 'O campo ID da Conta é obrigatório.',
            'idConta.exists' => 'A conta informada não existe.',
            'moeda.required' => 'O campo Moeda é obrigatório.',
            'moeda.string' => 'O campo Moeda deve ser uma string. (Texto)',
            'moeda.size' => 'O campo Moeda deve ter tamanho :size caracteres.',
            'valor.required' => 'O campo Valor é obrigatório.',
            'valor.numeric' => 'O campo Valor deve ser um número.',
            'valor.min' => 'O campo Valor deve ser no mínimo :min.',
        ];
    }


    // protected function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json(
    //         [
    //             'errors' => $validator->errors(),
    //             'status' => 'error'
    //         ],
    //         442
    //     ));
    // }
}
