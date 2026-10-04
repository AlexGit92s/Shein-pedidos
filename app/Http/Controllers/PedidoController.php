<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Pedido;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PedidoController extends Controller
{
    public function crear()
    {
        return view('pedido.crear', ['ajuste' => Ajuste::actual()]);
    }

    public function guardar(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:80',
            'telefono' => 'required|string|max:20|regex:/^[\d\s+()-]{8,}$/',
        ]);
        $articulos = $this->validarArticulos($request);

        $pedido = DB::transaction(function () use ($datos, $articulos) {
            $cliente = Cliente::updateOrCreate(
                ['telefono' => Cliente::normalizar($datos['telefono'])],
                ['nombre' => $datos['nombre']],
            );
            $pedido = Pedido::create([
                'cliente_id' => $cliente->id,
                'lote_id' => Lote::abierto()->id,
                'token' => Str::random(40),
            ]);
            $this->crearArticulos($pedido, $articulos);

            return $pedido;
        });

        return redirect()->route('pedido.ver', $pedido->token);
    }

    public function ver(string $token)
    {
        $pedido = Pedido::where('token', $token)->with('articulos', 'pagos', 'lote', 'cliente')->firstOrFail();

        return view('pedido.ver', ['pedido' => $pedido, 'ajuste' => Ajuste::actual()]);
    }

    // Reemplazos cuando algo salió agotado
    public function agregar(Request $request, string $token)
    {
        $pedido = Pedido::where('token', $token)->firstOrFail();
        abort_unless($pedido->aceptaArticulos(), 403, 'Este pedido ya no acepta artículos.');

        $this->crearArticulos($pedido, $this->validarArticulos($request));

        return redirect()->route('pedido.ver', $token)->with('ok', 'Artículos agregados.');
    }

    private function validarArticulos(Request $request): array
    {
        $esShein = function (string $attr, mixed $valor, Closure $fail) {
            if (! str_contains(strtolower((string) parse_url($valor, PHP_URL_HOST)), 'shein')) {
                $fail('El link debe ser de Shein.');
            }
        };

        return $request->validate([
            'articulos' => 'required|array|min:1|max:30',
            'articulos.*.link' => ['required', 'url:http,https', 'max:2000', $esShein],
            'articulos.*.talla' => 'nullable|string|max:30',
            'articulos.*.color' => 'nullable|string|max:30',
            'articulos.*.cantidad' => 'required|integer|min:1|max:20',
            'articulos.*.precio_usd' => 'required|numeric|min:0.01|max:1000',
        ])['articulos'];
    }

    private function crearArticulos(Pedido $pedido, array $articulos): void
    {
        $ajuste = Ajuste::actual();

        foreach ($articulos as $a) {
            $pedido->articulos()->create([
                'link' => $a['link'],
                'talla' => $a['talla'] ?? '',
                'color' => $a['color'] ?? '',
                'cantidad' => $a['cantidad'],
                'precio_usd' => $a['precio_usd'],
                'precio_lps' => $ajuste->precio((float) $a['precio_usd'], (int) $a['cantidad']),
            ]);
        }
    }
}
