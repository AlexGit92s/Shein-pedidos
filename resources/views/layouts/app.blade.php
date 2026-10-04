<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? \App\Models\Ajuste::actual()->nombre_negocio }}</title>
    {{-- ponytail: Tailwind por CDN; compilar con Vite si la página pesa o se vuelve lenta --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-pink-50 text-gray-800 min-h-screen">
    <main class="mx-auto p-4 @yield('ancho', 'max-w-xl')">
        @if (session('ok'))
            <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 p-3 text-red-800">
                @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
            </div>
        @endif
        @yield('contenido')
    </main>
</body>
</html>
