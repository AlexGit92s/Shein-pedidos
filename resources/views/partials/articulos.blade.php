{{-- Filas de artículos + total en vivo. Requiere $ajuste. --}}
<div id="articulos" class="space-y-3"></div>
<button type="button" id="agregar" class="mt-3 w-full rounded-lg border-2 border-dashed border-pink-300 p-3 text-pink-600">+ Agregar otro artículo</button>
<div class="mt-4 rounded-lg bg-white p-4 text-lg shadow">
    Total estimado: <strong id="total">L 0.00</strong>
    <p class="text-xs text-gray-500">Precio en Shein (USD) × L{{ $ajuste->tasa }} por dólar @if($ajuste->comision_pct) + {{ $ajuste->comision_pct + 0 }}% @endif @if($ajuste->cargo_fijo) + L{{ $ajuste->cargo_fijo + 0 }} por pieza @endif</p>
</div>

<template id="fila">
    <div class="fila rounded-lg bg-white p-3 shadow space-y-2">
        <input data-campo="link" type="url" required placeholder="Pega el link de Shein" class="w-full rounded border p-2">
        <div class="grid grid-cols-2 gap-2">
            <input data-campo="talla" placeholder="Talla" class="rounded border p-2">
            <input data-campo="color" placeholder="Color" class="rounded border p-2">
            <label class="text-sm">Cantidad<input data-campo="cantidad" type="number" min="1" max="20" value="1" required class="w-full rounded border p-2"></label>
            <label class="text-sm">Precio USD<input data-campo="precio_usd" type="number" step="0.01" min="0.01" required inputmode="decimal" class="w-full rounded border p-2"></label>
        </div>
        <div class="flex justify-between text-sm"><span class="subtotal text-gray-600"></span><button type="button" class="quitar text-red-500">Quitar</button></div>
    </div>
</template>

<script>
(() => {
    const cfg = @json(['tasa' => $ajuste->tasa, 'pct' => $ajuste->comision_pct, 'fijo' => $ajuste->cargo_fijo]);
    const previos = @json(old('articulos', []));
    const caja = document.getElementById('articulos');
    const tpl = document.getElementById('fila');
    let n = 0;
    // Misma fórmula que Ajuste::precio()
    const precio = (usd, cant) => Math.round((usd * cfg.tasa * (1 + cfg.pct / 100) + cfg.fijo) * cant * 100) / 100;
    const lps = v => 'L ' + v.toLocaleString('es-HN', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    function recalcular() {
        let total = 0;
        caja.querySelectorAll('.fila').forEach(f => {
            const usd = parseFloat(f.querySelector('[data-campo=precio_usd]').value) || 0;
            const cant = parseInt(f.querySelector('[data-campo=cantidad]').value) || 0;
            const sub = usd && cant ? precio(usd, cant) : 0;
            f.querySelector('.subtotal').textContent = sub ? lps(sub) : '';
            total += sub;
        });
        document.getElementById('total').textContent = lps(total);
    }

    function agregar(datos = {}) {
        const f = tpl.content.firstElementChild.cloneNode(true);
        const i = n++;
        f.querySelectorAll('[data-campo]').forEach(el => {
            el.name = `articulos[${i}][${el.dataset.campo}]`;
            if (datos[el.dataset.campo] != null) el.value = datos[el.dataset.campo];
        });
        f.querySelector('.quitar').onclick = () => { if (caja.children.length > 1) { f.remove(); recalcular(); } };
        caja.append(f);
        recalcular();
    }

    caja.addEventListener('input', recalcular);
    document.getElementById('agregar').onclick = () => agregar();
    (Object.values(previos).length ? Object.values(previos) : [{}]).forEach(agregar);
})();
</script>
