<?php

declare(strict_types=1);

return [
    'input' => [
        'nombre' => 'First name',
        'primer_apellido' => 'Last name',
        'segundo_apellido' => 'Second last name',
        'email' => 'Email address',
        'password' => 'Password',
        'password_confirmation' => 'Confirm password',
    ],
    'password_rules' => [
        'min_length' => 'At least 8 characters',
        'mixed_case' => 'Uppercase and lowercase letters',
        'numbers' => 'At least one number',
        'symbols' => 'At least one symbol',
    ],
    'models' => [
        'usuario' => 'User',
    ],
    'acciones' => 'Actions',
    'ordenacion' => [
        'atributo' => 'sort',
        'atributo_direccion' => 'sort direction',
        'ordenar_ascendente' => 'Sort by :columna ascending',
        'ordenar_descendente' => 'Sort by :columna descending',
        'quitar_ordenacion' => 'Remove sort by :columna',
    ],
    'usuarios' => [
        'titulo' => 'Users',
        'vacio' => 'No users found.',
        'detalle' => 'User details',
        'datos' => 'User data',
        'cambio_password' => 'Change password',
        'password_opcional_aviso' => 'If no password is provided, a random one will be generated. The user will have to change the password through an email message via the "I forgot my password" option.',
    ],
];
