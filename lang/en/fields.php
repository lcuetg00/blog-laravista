<?php

declare(strict_types=1);

return [
    'input' => [
        'nombre' => 'First name',
        'nombre_completo' => 'Full name',
        'primer_apellido' => 'Last name',
        'segundo_apellido' => 'Second last name',
        'email' => 'Email address',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
        'busqueda' => 'Text search',
        'nombre_rol' => 'Role name',
        'descripcion' => 'Description',
    ],
    'password_rules' => [
        'min_length' => 'At least 8 characters',
        'mixed_case' => 'Uppercase and lowercase letters',
        'numbers' => 'At least one number',
        'symbols' => 'At least one symbol',
    ],
    'models' => [
        'usuario' => 'User',
        'rol' => 'Role',
    ],
    'acciones' => 'Actions',
    'sin_registros' => 'No records found.',
    'ordenacion' => [
        'atributo' => 'sort',
        'atributo_direccion' => 'sort direction',
        'ordenar_ascendente' => 'Sort by :columna ascending',
        'ordenar_descendente' => 'Sort by :columna descending',
        'quitar_ordenacion' => 'Remove sort by :columna',
    ],
    'usuarios' => [
        'titulo' => 'Users',
        'detalle' => 'User details',
        'datos' => 'User data',
        'cambio_password' => 'Change password',
        'password_opcional_aviso' => 'If no password is provided, a random one will be generated. The user will have to change the password through an email message via the "I forgot my password" option.',
    ],
    'roles' => [
        'titulo' => 'Roles',
        'detalle' => 'Role details',
        'datos' => 'Role data',
    ],
];
