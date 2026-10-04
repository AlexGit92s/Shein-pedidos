<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $guarded = [];

    // Solo dígitos; 8 dígitos se asume Honduras (504).
    public static function normalizar(string $tel): string
    {
        $tel = preg_replace('/\D/', '', $tel);

        return strlen($tel) === 8 ? '504'.$tel : $tel;
    }

    public static function wa(string $tel, string $texto): string
    {
        return 'https://wa.me/'.self::normalizar($tel).'?text='.rawurlencode($texto);
    }
}
