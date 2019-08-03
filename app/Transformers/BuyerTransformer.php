<?php

namespace App\Transformers;

use App\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Buyer $buyer)
    {
        return [
            'identificador' => (int)$buyer->id,
            'nombre' => (string)$buyer->name,
            'correo' => (string)$buyer->email,
            'esVerificado' => (int)$buyer->verified,
            'fechaCreacion' => (string)$buyer->created_at,
            'fechaActualizacion' => (string)$buyer->updated_at,
            'fechaEliminacion' => isset($buyer->deleted_at) ? (string) $buyer->deleted_at : null,
        ];
    }

     public static function originalAttribute($index){

            $attributes = [
                'identificador' => 'id',
                'nombre' => 'name',
                'correo' => 'email',
                'esVerificado' => 'verified',
                'fechaCreacion' => 'created_at',
                'fechaActualizacion' => 'updated_at',
                'fechaEliminacion' => 'deleted_at',
            ];

            return isset($attributes[$index])  ? $attributes[$index] : null;
    }

    public static function transformAttribute($index){

            $attributes = [
                    'id' => 'identificador',
                    'name' => 'nombre',
                    'email' => 'correo',
                    'verified' => 'esVerificado',
                    'created_at' => 'fechaCreacion',
                    'updated_at' => 'fechaActualizacion',
                    'deleted_at' => 'fechaEliminacion',
            ];

            return isset($attributes[$index])  ? $attributes[$index] : null;
    }
}
