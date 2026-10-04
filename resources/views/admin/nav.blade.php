<nav class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.lote') }}" class="text-xl font-bold text-pink-600">Panel</a>
    <a href="{{ route('admin.ajustes') }}" class="underline">Ajustes</a>
    <a href="{{ route('pedido.crear') }}" target="_blank" class="underline">Página de clientes</a>
    <form method="post" action="{{ route('logout') }}" class="ml-auto">@csrf<button class="text-gray-500 underline">Salir</button></form>
</nav>
