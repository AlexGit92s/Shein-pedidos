@extends('layouts.app')

@php
    $url = route('pedido.ver', $pedido->token);
    $wa = $ajuste->whatsapp
        ? \App\Models\Cliente::wa($ajuste->whatsapp, "Hola, soy {$pedido->cliente->nombre}. Este es mi pedido: {$url}")
        : null;
    $estadoLote = ['abierto' => 'Recibiendo pedidos', 'comprado' => 'Comprado en Shein', 'en_camino' => 'En camino', 'llegó' => 'Ya llegó, lista para entregar', 'cerrado' => 'Cerrado'];
@endphp

@section('contenido')
<h1 class="text-2xl font-bold text-pink-600">{{ $ajuste->nombre_negocio }}</h1>
<p class="mb-1">Pedido de <strong>{{ $pedido->cliente->nombre }}</strong></p>
<p class="mb-4 text-sm text-gray-600">Estado: {{ $pedido->estado === 'entregado' ? 'Entregado' : $estadoLote[$pedido->lote->estado] }}</p>

<div class="mb-4 rounded-lg bg-yellow-50 p-3 text-sm">Guarda este enlace para ver tu pedido cuando quieras.</div>

<div class="space-y-2">
    @foreach ($pedido->articulos as $a)
        <div class="rounded-lg bg-white p-3 shadow {{ $a->estado === 'agotado' ? 'opacity-60' : '' }}">
            <a href="{{ $a->link }}" target="_blank" rel="noopener noreferrer" class="block truncate text-pink-600 underline">{{ $a->link }}</a>
            <div class="flex justify-between text-sm">
                <span>{{ $a->talla }} {{ $a->color }} · {{ $a->cantidad }} pz · ${{ number_format($a->precio_usd, 2) }}</span>
                <span class="{{ $a->estado === 'agotado' ? 'line-through' : '' }}">L {{ number_format($a->precio_lps, 2) }}</span>
            </div>
            @if ($a->estado === 'agotado')
                <p class="mt-1 text-sm font-semibold text-red-600">Ya no hay en Shein. No se te cobra; puedes agregar otro abajo.</p>
            @endif
        </div>
    @endforeach
</div>

<div class="mt-4 space-y-1 rounded-lg bg-white p-4 shadow">
    <div class="flex justify-between"><span>Total</span><strong>L {{ number_format($pedido->total(), 2) }}</strong></div>
    <div class="flex justify-between"><span>Pagado</span><span>L {{ number_format($pedido->pagado(), 2) }}</span></div>
    <div class="flex justify-between text-lg"><span>{{ $pedido->saldo() < 0 ? 'A tu favor' : 'Pendiente' }}</span><strong>L {{ number_format(abs($pedido->saldo()), 2) }}</strong></div>
</div>

@if ($wa)
    <a href="{{ $wa }}" target="_blank" class="mt-4 block rounded-lg bg-green-600 p-3 text-center text-lg font-semibold text-white">Confirmar por WhatsApp</a>
@endif

@if ($pedido->aceptaArticulos())
    <details class="mt-6" @if($errors->any()) open @endif>
        <summary class="cursor-pointer font-semibold text-pink-600">Agregar más artículos</summary>
        <form method="post" action="{{ route('pedido.agregar', $pedido->token) }}" class="mt-3">
            @csrf
            @include('partials.articulos')
            <button class="mt-4 w-full rounded-lg bg-pink-600 p-3 font-semibold text-white">Agregar</button>
        </form>
    </details>
@endif
@endsection
