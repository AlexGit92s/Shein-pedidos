<?php

namespace Tests\Feature;

use App\Models\Ajuste;
use App\Models\Lote;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PedidosTest extends TestCase
{
    use RefreshDatabase;

    private function enviarPedido(array $articulos, string $tel = '9999-8888')
    {
        return $this->post('/', ['nombre' => 'Ana', 'telefono' => $tel, 'articulos' => $articulos]);
    }

    private function articulo(float $usd = 10, int $cant = 1): array
    {
        return ['link' => 'https://us.shein.com/blusa-p-123.html', 'talla' => 'M', 'cantidad' => $cant, 'precio_usd' => $usd];
    }

    public function test_formula_de_precio(): void
    {
        $a = new Ajuste(['tasa' => 25, 'comision_pct' => 20, 'cargo_fijo' => 50]);

        // (10 × 25 × 1.2 + 50) × 2 = 700
        $this->assertSame(700.0, $a->precio(10, 2));
    }

    public function test_cliente_crea_pedido_con_precio_congelado(): void
    {
        Ajuste::actual()->update(['tasa' => 25, 'comision_pct' => 10, 'cargo_fijo' => 0]);

        $this->enviarPedido([$this->articulo(10, 2)])->assertRedirect();

        $pedido = Pedido::with('cliente', 'articulos')->sole();
        $this->assertSame('50499998888', $pedido->cliente->telefono);
        $this->assertSame(550.0, $pedido->total());

        // Cambiar la tasa no altera pedidos ya hechos
        Ajuste::actual()->update(['tasa' => 30]);
        $this->assertSame(550.0, $pedido->fresh()->total());

        $this->get('/p/'.$pedido->token)->assertOk()->assertSee('550.00');
    }

    public function test_rechaza_links_que_no_son_de_shein(): void
    {
        $malo = ['link' => 'https://evil.com/x'] + $this->articulo();
        $this->enviarPedido([$malo])->assertSessionHasErrors('articulos.0.link');

        $js = ['link' => 'javascript:alert(1)'] + $this->articulo();
        $this->enviarPedido([$js])->assertSessionHasErrors('articulos.0.link');

        $this->assertSame(0, Pedido::count());
    }

    public function test_agotado_y_pagos_calculan_saldo_y_ganancia(): void
    {
        Ajuste::actual()->update(['tasa' => 25]);
        $this->enviarPedido([$this->articulo(10), $this->articulo(20)]); // 250 + 500
        $pedido = Pedido::sole();
        $admin = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.articulo.actualizar', $pedido->articulos()->first()), ['estado' => 'agotado']);
        $this->actingAs($admin)->post(route('admin.pago.crear', $pedido), ['monto_lps' => 200]);

        $pedido = $pedido->fresh();
        $this->assertSame(500.0, $pedido->total());
        $this->assertSame(300.0, $pedido->saldo());

        $lote = $pedido->lote;
        $lote->update(['costo_shein_lps' => 300, 'costo_courier_lps' => 50]);
        $this->assertSame(150.0, $lote->fresh()->ganancia());
    }

    public function test_al_comprar_el_lote_los_nuevos_pedidos_van_a_otro(): void
    {
        $this->enviarPedido([$this->articulo()]);
        $lote = Lote::sole();

        $this->actingAs(User::factory()->create())
            ->patch(route('admin.lote.actualizar', $lote), ['estado' => 'comprado', 'costo_shein_lps' => 0, 'costo_courier_lps' => 0]);

        $this->enviarPedido([$this->articulo()], '3333-4444');
        $this->assertSame(2, Lote::count());
        $this->assertNotSame($lote->id, Pedido::latest('id')->first()->lote_id);
    }

    public function test_panel_requiere_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->post(route('admin.pago.crear', 1), ['monto_lps' => 10])->assertRedirect('/login');
    }

    public function test_panel_carga(): void
    {
        $this->enviarPedido([$this->articulo()]);

        $this->actingAs(User::factory()->create())->get('/admin')->assertOk()->assertSee('Ana');
        $this->actingAs(User::factory()->create())->get('/admin/ajustes')->assertOk();
    }
}
