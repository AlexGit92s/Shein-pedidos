<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajustes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_negocio')->default('Mi Tienda');
            $table->string('whatsapp')->default('');
            $table->decimal('tasa', 8, 2)->default(25);          // lempiras por dólar
            $table->decimal('comision_pct', 5, 2)->default(0);
            $table->decimal('cargo_fijo', 10, 2)->default(0);    // lempiras por unidad
            $table->timestamps();
        });

        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->string('estado')->default('abierto');
            $table->decimal('costo_shein_lps', 12, 2)->default(0);
            $table->decimal('costo_courier_lps', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono')->unique();
            $table->timestamps();
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained();
            $table->foreignId('lote_id')->constrained();
            $table->string('token', 40)->unique();
            $table->string('estado')->default('nuevo');
            $table->timestamps();
        });

        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained()->cascadeOnDelete();
            $table->text('link');
            $table->string('talla')->default('');
            $table->string('color')->default('');
            $table->unsignedSmallInteger('cantidad');
            $table->decimal('precio_usd', 10, 2);
            $table->decimal('precio_lps', 12, 2);   // total de la fila, congelado al crear
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained()->cascadeOnDelete();
            $table->decimal('monto_lps', 12, 2);
            $table->string('nota')->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['pagos', 'articulos', 'pedidos', 'clientes', 'lotes', 'ajustes'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
