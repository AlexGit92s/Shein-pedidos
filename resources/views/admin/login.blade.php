@extends('layouts.app')

@section('contenido')
<form method="post" action="/login" class="mt-20 space-y-3 rounded-lg bg-white p-6 shadow">
    @csrf
    <h1 class="text-xl font-bold">Entrar al panel</h1>
    <input name="email" type="email" value="{{ old('email') }}" required placeholder="Correo" class="w-full rounded border p-2">
    <input name="password" type="password" required placeholder="Contraseña" class="w-full rounded border p-2">
    <button class="w-full rounded bg-pink-600 p-2 font-semibold text-white">Entrar</button>
</form>
@endsection
