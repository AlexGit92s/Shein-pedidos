<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lote extends Model
{
    public const ESTADOS = ['abierto', 'comprado', 'en_camino', 'llegó', 'cerrado'];

    protected $guarded = [];

    protected $casts = ['costo_shein_lps' => 'float', 'costo_courier_lps' => 'float'];

    // Los pedidos nuevos caen aquí; cuando ella lo pasa a "comprado" se abre otro.
    public static function abierto(): self
    {
        return self::firstOrCreate(['estado' => 'abierto']);
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function vendido(): float
    {
        return $this->pedidos->sum(fn (Pedido $p) => $p->total());
    }

    public function ganancia(): float
    {
        return round($this->vendido() - $this->costo_shein_lps - $this->costo_courier_lps, 2);
    }
}
