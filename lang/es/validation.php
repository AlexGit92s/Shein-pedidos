<?php

// Solo las reglas que usa la app; si se agrega una regla nueva, agregar su mensaje aquí.
return [
    'array' => 'El campo :attribute no es válido.',
    'email' => 'Escribe un correo válido.',
    'in' => 'El valor de :attribute no es válido.',
    'integer' => ':Attribute debe ser un número entero.',
    'numeric' => ':Attribute debe ser un número.',
    'required' => 'Falta :attribute.',
    'regex' => 'El formato de :attribute no es válido.',
    'string' => 'El campo :attribute no es válido.',
    'url' => ':Attribute debe ser un link válido.',
    'max' => [
        'array' => 'Máximo :max artículos por pedido.',
        'numeric' => ':Attribute no puede ser mayor que :max.',
        'string' => ':Attribute es demasiado largo (máximo :max caracteres).',
    ],
    'min' => [
        'array' => 'Agrega al menos :min artículo.',
        'numeric' => ':Attribute debe ser al menos :min.',
        'string' => ':Attribute debe tener al menos :min caracteres.',
    ],
    'attributes' => [
        'nombre' => 'tu nombre',
        'telefono' => 'tu WhatsApp',
        'articulos' => 'artículos',
        'articulos.*.link' => 'el link',
        'articulos.*.talla' => 'la talla',
        'articulos.*.color' => 'el color',
        'articulos.*.cantidad' => 'la cantidad',
        'articulos.*.precio_usd' => 'el precio',
        'monto_lps' => 'el monto',
        'nota' => 'la nota',
        'password' => 'la contraseña',
        'nombre_negocio' => 'el nombre del negocio',
        'tasa' => 'la tasa',
        'comision_pct' => 'la comisión',
        'cargo_fijo' => 'el cargo fijo',
        'costo_shein_lps' => 'lo pagado a Shein',
        'costo_courier_lps' => 'el courier',
    ],
];
