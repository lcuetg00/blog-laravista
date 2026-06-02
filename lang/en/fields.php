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
    'usuarios' => [
        'titulo' => 'Users',
        'vacio' => 'No users found.',
        'detalle' => 'User details',
    ],
];
