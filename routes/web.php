<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PedidoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Cliente (sin login)
Route::get('/', [PedidoController::class, 'crear'])->name('pedido.crear');
Route::post('/', [PedidoController::class, 'guardar'])->middleware('throttle:10,1')->name('pedido.guardar');
Route::get('/p/{token}', [PedidoController::class, 'ver'])->name('pedido.ver');
Route::post('/p/{token}', [PedidoController::class, 'agregar'])->middleware('throttle:10,1')->name('pedido.agregar');

// Login de la administradora
Route::view('/login', 'admin.login')->name('login');
Route::post('/login', function (Request $request) {
    $cred = $request->validate(['email' => 'required|email', 'password' => 'required']);
    if (! Auth::attempt($cred, true)) {
        return back()->withErrors(['email' => 'Correo o contraseña incorrectos.'])->onlyInput('email');
    }
    $request->session()->regenerate();

    return redirect()->intended(route('admin.lote'));
})->middleware('throttle:5,1');
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();

    return redirect('/login');
})->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::get('/', 'lote')->name('lote');
    Route::patch('/lotes/{lote}', 'actualizarLote')->name('lote.actualizar');
    Route::patch('/articulos/{articulo}', 'actualizarArticulo')->name('articulo.actualizar');
    Route::patch('/pedidos/{pedido}', 'actualizarPedido')->name('pedido.actualizar');
    Route::post('/pedidos/{pedido}/pagos', 'pagar')->name('pago.crear');
    Route::delete('/pagos/{pago}', 'borrarPago')->name('pago.borrar');
    Route::get('/ajustes', 'ajustes')->name('ajustes');
    Route::put('/ajustes', 'guardarAjustes')->name('ajustes.guardar');
});
