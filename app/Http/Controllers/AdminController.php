<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Articulo;
use App\Models\Lote;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function lote(Request $request)
    {
        $lote = $request->filled('lote') ? Lote::findOrFail($request->integer('lote')) : Lote::abierto();
        $lote->load('pedidos.cliente', 'pedidos.articulos', 'pedidos.pagos');

        return view('admin.lote', [
            'lote' => $lote,
            'lotes' => Lote::latest('id')->get(),
            'ajuste' => Ajuste::actual(),
        ]);
    }

    public function actualizarLote(Request $request, Lote $lote)
    {
        $datos = $request->validate([
            'estado' => ['required', Rule::in(Lote::ESTADOS)],
            'costo_shein_lps' => 'required|numeric|min:0',
            'costo_courier_lps' => 'required|numeric|min:0',
        ]);

        if ($datos['estado'] === 'abierto' && Lote::where('estado', 'abierto')->whereKeyNot($lote->id)->exists()) {
            return back()->withErrors(['estado' => 'Ya hay otro lote abierto.']);
        }

        $lote->update($datos);

        return back()->with('ok', 'Lote actualizado.');
    }

    public function actualizarArticulo(Request $request, Articulo $articulo)
    {
        $articulo->update($request->validate(['estado' => ['required', Rule::in(Articulo::ESTADOS)]]));

        return back();
    }

    public function actualizarPedido(Request $request, Pedido $pedido)
    {
        $pedido->update($request->validate(['estado' => ['required', Rule::in(Pedido::ESTADOS)]]));

        return back();
    }

    public function pagar(Request $request, Pedido $pedido)
    {
        $datos = $request->validate([
            'monto_lps' => 'required|numeric|min:0.01|max:1000000',
            'nota' => 'nullable|string|max:100',
        ]);
        $pedido->pagos()->create(['nota' => $datos['nota'] ?? ''] + $datos);

        return back()->with('ok', 'Pago registrado.');
    }

    public function borrarPago(Pago $pago)
    {
        $pago->delete();

        return back()->with('ok', 'Pago eliminado.');
    }

    public function ajustes()
    {
        return view('admin.ajustes', ['ajuste' => Ajuste::actual()]);
    }

    public function guardarAjustes(Request $request)
    {
        $datos = $request->validate([
            'nombre_negocio' => 'required|string|max:60',
            'whatsapp' => 'nullable|string|max:20',
            'tasa' => 'required|numeric|min:0.01|max:1000',
            'comision_pct' => 'required|numeric|min:0|max:500',
            'cargo_fijo' => 'required|numeric|min:0|max:100000',
        ]);
        Ajuste::actual()->update(['whatsapp' => $datos['whatsapp'] ?? ''] + $datos);

        return back()->with('ok', 'Ajustes guardados.');
    }
}
