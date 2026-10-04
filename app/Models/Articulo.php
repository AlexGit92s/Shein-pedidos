<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Articulo extends Model
{
    public const ESTADOS = ['pendiente', 'comprado', 'agotado'];

    protected $guarded = [];

    protected $casts = ['precio_usd' => 'float', 'precio_lps' => 'float'];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
