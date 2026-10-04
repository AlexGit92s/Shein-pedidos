<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    public const ESTADOS = ['nuevo', 'confirmado', 'entregado'];

    protected $guarded = [];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function total(): float
    {
        return round($this->articulos->where('estado', '!=', 'agotado')->sum('precio_lps'), 2);
    }

    public function pagado(): float
    {
        return round($this->pagos->sum('monto_lps'), 2);
    }

    // Negativo = saldo a favor del cliente
    public function saldo(): float
    {
        return round($this->total() - $this->pagado(), 2);
    }

    // El cliente puede agregar reemplazos mientras ella aún puede comprarlos.
    public function aceptaArticulos(): bool
    {
        return in_array($this->lote->estado, ['abierto', 'comprado']);
    }
}
