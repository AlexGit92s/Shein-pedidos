@extends('layouts.app')
@section('ancho', 'max-w-7xl')

@use('App\Models\Cliente')
@php
    $articulos = $lote->pedidos->flatMap(fn ($p) => $p->articulos->each->setRelation('pedido', $p));
    $vigentes = $articulos->where('estado', '!=', 'agotado');
    $cobrado = $lote->pedidos->sum(fn ($p) => $p->pagado());
    $verUrl = fn ($p) => route('pedido.ver', $p->token);
    $L = fn ($v) => 'L '.number_format($v, 2);
@endphp

@section('contenido')
@include('admin.nav')

<div class="mb-6 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
    <form method="get">
        <label class="text-sm">Lote
            <select name="lote" onchange="this.form.submit()" class="block rounded border p-2">
                @foreach ($lotes as $l)
                    <option value="{{ $l->id }}" @selected($l->id === $lote->id)>#{{ $l->id }} · {{ $l->estado }} · {{ $l->created_at->format('d/m/Y') }}</option>
                @endforeach
            </select>
        </label>
    </form>
    <form method="post" action="{{ route('admin.lote.actualizar', $lote) }}" class="flex flex-wrap items-end gap-3">
        @csrf @method('patch')
        <label class="text-sm">Estado
            <select name="estado" class="block rounded border p-2">
                @foreach (\App\Models\Lote::ESTADOS as $e) <option @selected($lote->estado === $e)>{{ $e }}</option> @endforeach
            </select>
        </label>
        <label class="text-sm">Pagado a Shein (L)<input name="costo_shein_lps" type="number" step="0.01" min="0" value="{{ $lote->costo_shein_lps }}" class="block w-36 rounded border p-2"></label>
        <label class="text-sm">Courier/envío (L)<input name="costo_courier_lps" type="number" step="0.01" min="0" value="{{ $lote->costo_courier_lps }}" class="block w-36 rounded border p-2"></label>
        <button class="rounded bg-pink-600 px-4 py-2 text-white">Guardar</button>
    </form>
    @if ($lote->estado === 'abierto')
        <p class="text-sm text-gray-500">Al pasarlo a "comprado", los pedidos nuevos caen en un lote nuevo.</p>
    @endif
</div>

<div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-5">
    @foreach ([
        'Vendido' => $L($lote->vendido()),
        'Cobrado' => $L($cobrado),
        'Por cobrar' => $L($lote->vendido() - $cobrado),
        'Costo' => $L($lote->costo_shein_lps + $lote->costo_courier_lps),
        'Ganancia' => $L($lote->ganancia()),
    ] as $k => $v)
        <div class="rounded-lg bg-white p-4 shadow"><div class="text-sm text-gray-500">{{ $k }}</div><div class="text-xl font-bold">{{ $v }}</div></div>
    @endforeach
</div>

<h2 class="mb-2 text-lg font-bold">Lista de compra ({{ $vigentes->sum('cantidad') }} piezas · ${{ number_format($vigentes->sum(fn ($a) => $a->precio_usd * $a->cantidad), 2) }} en Shein)</h2>
<div class="mb-8 overflow-x-auto rounded-lg bg-white shadow">
    <table class="w-full text-sm">
        <thead class="bg-pink-100 text-left"><tr><th class="p-2">Cliente</th><th>Link</th><th>Talla</th><th>Color</th><th>Cant</th><th>USD</th><th>Lps</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @forelse ($articulos as $a)
            <tr class="border-t {{ $a->estado === 'agotado' ? 'bg-red-50' : ($a->estado === 'comprado' ? 'bg-green-50' : '') }}">
                <td class="p-2">{{ $a->pedido->cliente->nombre }}</td>
                <td class="max-w-xs truncate"><a href="{{ $a->link }}" target="_blank" rel="noopener noreferrer" class="text-pink-600 underline">{{ $a->link }}</a></td>
                <td>{{ $a->talla }}</td><td>{{ $a->color }}</td><td>{{ $a->cantidad }}</td>
                <td>${{ number_format($a->precio_usd, 2) }}</td><td>{{ $L($a->precio_lps) }}</td>
                <td>
                    <form method="post" action="{{ route('admin.articulo.actualizar', $a) }}">@csrf @method('patch')
                        <select name="estado" onchange="this.form.submit()" class="rounded border p-1">
                            @foreach (\App\Models\Articulo::ESTADOS as $e) <option @selected($a->estado === $e)>{{ $e }}</option> @endforeach
                        </select>
                    </form>
                </td>
                <td class="pr-2">
                    @if ($a->estado === 'agotado')
                        <a target="_blank" class="text-green-700 underline" href="{{ Cliente::wa($a->pedido->cliente->telefono, "Hola {$a->pedido->cliente->nombre}, el artículo {$a->link} (talla {$a->talla}) ya no está disponible en Shein. No se te cobra; puedes elegir otro y agregarlo aquí: {$verUrl($a->pedido)}") }}">Avisar</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="p-4 text-center text-gray-500">Aún no hay pedidos en este lote.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<h2 class="mb-2 text-lg font-bold">Clientes</h2>
<div class="grid gap-4 md:grid-cols-2">
    @foreach ($lote->pedidos as $p)
        @php $c = $p->cliente; $resumen = "Total {$L($p->total())}, pagado {$L($p->pagado())}, saldo {$L($p->saldo())}. Detalle: {$verUrl($p)}"; @endphp
        <div class="rounded-lg bg-white p-4 shadow">
            <div class="flex items-start justify-between">
                <div>
                    <a href="{{ $verUrl($p) }}" target="_blank" class="font-bold underline">{{ $c->nombre }}</a>
                    <div class="text-sm text-gray-500">{{ $c->telefono }} · {{ $p->articulos->count() }} artículos · pedido #{{ $p->id }}</div>
                </div>
                <form method="post" action="{{ route('admin.pedido.actualizar', $p) }}">@csrf @method('patch')
                    <select name="estado" onchange="this.form.submit()" class="rounded border p-1 text-sm">
                        @foreach (\App\Models\Pedido::ESTADOS as $e) <option @selected($p->estado === $e)>{{ $e }}</option> @endforeach
                    </select>
                </form>
            </div>
            <div class="my-2 grid grid-cols-3 text-center">
                <div><div class="text-xs text-gray-500">Total</div>{{ $L($p->total()) }}</div>
                <div><div class="text-xs text-gray-500">Pagado</div>{{ $L($p->pagado()) }}</div>
                <div><div class="text-xs text-gray-500">Saldo</div><strong class="{{ $p->saldo() > 0 ? 'text-red-600' : 'text-green-700' }}">{{ $L($p->saldo()) }}</strong></div>
            </div>
            @foreach ($p->pagos as $pg)
                <form method="post" action="{{ route('admin.pago.borrar', $pg) }}" class="flex justify-between text-sm text-gray-600" onsubmit="return confirm('¿Eliminar este pago?')">
                    @csrf @method('delete')
                    <span>{{ $pg->created_at->format('d/m') }} · {{ $L($pg->monto_lps) }} {{ $pg->nota }}</span>
                    <button class="text-red-500">×</button>
                </form>
            @endforeach
            <form method="post" action="{{ route('admin.pago.crear', $p) }}" class="mt-2 flex gap-2">
                @csrf
                <input name="monto_lps" type="number" step="0.01" min="0.01" required placeholder="Monto L" class="w-28 rounded border p-1">
                <input name="nota" maxlength="100" placeholder="Nota (anticipo, transferencia...)" class="flex-1 rounded border p-1">
                <button class="rounded bg-gray-800 px-3 text-white">Pago</button>
            </form>
            <div class="mt-3 flex gap-4 text-sm">
                <a target="_blank" class="text-green-700 underline" href="{{ Cliente::wa($c->telefono, "Hola {$c->nombre}, tu pedido: {$resumen}") }}">WhatsApp: saldo</a>
                <a target="_blank" class="text-green-700 underline" href="{{ Cliente::wa($c->telefono, "Hola {$c->nombre}, ¡tu pedido de Shein llegó! {$resumen}") }}">WhatsApp: llegó</a>
            </div>
        </div>
    @endforeach
</div>
@endsection
