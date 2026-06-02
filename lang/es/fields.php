<?php

declare(strict_types=1);

return [
    'input' => [
        'nombre' => 'Nombre',
        'primer_apellido' => 'Primer apellido',
        'segundo_apellido' => 'Segundo apellido',
        'email' => 'Correo electrónico',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar contraseña',
    ],
    'password_rules' => [
        'min_length' => 'Mínimo 8 caracteres',
        'mixed_case' => 'Mayúsculas y minúsculas',
        'numbers' => 'Al menos un número',
        'symbols' => 'Al menos un símbolo',
    ],
    'models' => [
        'usuario' => 'Usuario',
    ],
    'acciones' => 'Acciones',
    'usuarios' => [
        'titulo' => 'Usuarios',
        'vacio' => 'No hay usuarios.',
        'detalle' => 'Detalle del usuario',
    ],
];
