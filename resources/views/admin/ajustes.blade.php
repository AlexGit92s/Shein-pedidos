@extends('layouts.app')
@section('ancho', 'max-w-3xl')

@section('contenido')
@include('admin.nav')
<form method="post" action="{{ route('admin.ajustes.guardar') }}" class="space-y-4 rounded-lg bg-white p-6 shadow">
    @csrf @method('put')
    <label class="block">Nombre del negocio<input name="nombre_negocio" value="{{ old('nombre_negocio', $ajuste->nombre_negocio) }}" required class="w-full rounded border p-2"></label>
    <label class="block">Tu WhatsApp (los clientes te escriben aquí)<input name="whatsapp" value="{{ old('whatsapp', $ajuste->whatsapp) }}" placeholder="9999-9999" class="w-full rounded border p-2"></label>
    <div class="grid grid-cols-3 gap-4">
        <label>Tasa (L por $)<input name="tasa" type="number" step="0.01" value="{{ old('tasa', $ajuste->tasa) }}" required class="w-full rounded border p-2"></label>
        <label>Comisión %<input name="comision_pct" type="number" step="0.01" value="{{ old('comision_pct', $ajuste->comision_pct) }}" required class="w-full rounded border p-2"></label>
        <label>Cargo fijo por pieza (L)<input name="cargo_fijo" type="number" step="0.01" value="{{ old('cargo_fijo', $ajuste->cargo_fijo) }}" required class="w-full rounded border p-2"></label>
    </div>
    <p class="text-sm text-gray-600">Precio al cliente = precio USD × tasa × (1 + comisión%) + cargo fijo, por cada pieza. Los cambios aplican a pedidos nuevos; los ya hechos conservan su precio.</p>
    <button class="rounded bg-pink-600 px-6 py-2 font-semibold text-white">Guardar</button>
</form>
@endsection
