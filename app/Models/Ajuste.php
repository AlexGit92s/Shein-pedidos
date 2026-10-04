<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    protected $guarded = [];

    protected $casts = ['tasa' => 'float', 'comision_pct' => 'float', 'cargo_fijo' => 'float'];

    public static function actual(): self
    {
        return self::firstOrCreate([]);
    }

    // Misma fórmula que el cotizador en resources/views/pedido/crear.blade.php
    public function precio(float $usd, int $cantidad): float
    {
        return round(($usd * $this->tasa * (1 + $this->comision_pct / 100) + $this->cargo_fijo) * $cantidad, 2);
    }
}
