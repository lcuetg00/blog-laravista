<?php

declare(strict_types=1);

return [
    'input' => [
        'nombre' => 'Nombre',
        'nombre_completo' => 'Nombre completo',
        'primer_apellido' => 'Primer apellido',
        'segundo_apellido' => 'Segundo apellido',
        'email' => 'Correo electrónico',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar contraseña',
        'busqueda' => 'Búsqueda por texto',
        'nombre_rol' => 'Nombre del rol',
        'descripcion' => 'Descripción',
    ],
    'password_rules' => [
        'min_length' => 'Mínimo 8 caracteres',
        'mixed_case' => 'Mayúsculas y minúsculas',
        'numbers' => 'Al menos un número',
        'symbols' => 'Al menos un símbolo',
    ],
    'models' => [
        'usuario' => 'Usuario',
        'rol' => 'Rol',
    ],
    'acciones' => 'Acciones',
    'sin_registros' => 'No se han encontrado registros.',
    'ordenacion' => [
        'atributo' => 'ordenación',
        'atributo_direccion' => 'dirección de ordenación',
        'ordenar_ascendente' => 'Ordenar por :columna ascendente',
        'ordenar_descendente' => 'Ordenar por :columna descendente',
        'quitar_ordenacion' => 'Quitar la ordenación por :columna',
    ],
    'usuarios' => [
        'titulo' => 'Usuarios',
        'detalle' => 'Detalle del usuario',
        'datos' => 'Datos del usuario',
        'cambio_password' => 'Cambio de contraseña',
        'password_opcional_aviso' => 'Si no introduce una contraseña, se generará una aleatoria. El usuario tendrá que cambiar la contraseña con un mensaje al correo electrónico en la opción de «He olvidado mi contraseña».',
    ],
    'roles' => [
        'titulo' => 'Roles',
        'detalle' => 'Detalle del rol',
        'datos' => 'Datos del rol',
    ],
];
