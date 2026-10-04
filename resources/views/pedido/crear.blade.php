@extends('layouts.app')

@section('contenido')
<h1 class="text-2xl font-bold text-pink-600">{{ $ajuste->nombre_negocio }}</h1>
<p class="mb-4 text-gray-600">Cotiza y envía tu pedido de Shein. Pega el link de cada artículo y escribe el precio que ves en la app (en dólares).</p>

<form method="post" action="{{ route('pedido.guardar') }}">
    @csrf
    <div class="mb-4 space-y-2 rounded-lg bg-white p-3 shadow">
        <input name="nombre" value="{{ old('nombre') }}" required maxlength="80" placeholder="Tu nombre" class="w-full rounded border p-2">
        <input name="telefono" value="{{ old('telefono') }}" required type="tel" inputmode="tel" placeholder="Tu WhatsApp (ej. 9999-9999)" class="w-full rounded border p-2">
    </div>

    @include('partials.articulos')

    <button class="mt-4 w-full rounded-lg bg-pink-600 p-3 text-lg font-semibold text-white">Enviar pedido</button>
    <p class="mt-2 text-center text-xs text-gray-500">El total es un estimado; se confirma al hacer la compra en Shein.</p>
</form>
@endsection
