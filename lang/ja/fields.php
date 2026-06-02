<?php

declare(strict_types=1);

return [
    'input' => [
        'nombre' => '名',
        'primer_apellido' => '姓',
        'segundo_apellido' => 'ミドルネーム',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認）',
    ],
    'password_rules' => [
        'min_length' => '8文字以上',
        'mixed_case' => '大文字と小文字を含む',
        'numbers' => '数字を1文字以上含む',
        'symbols' => '記号を1文字以上含む',
    ],
    'models' => [
        'usuario' => 'ユーザー',
    ],
    'acciones' => 'アクション',
    'usuarios' => [
        'titulo' => 'ユーザー',
        'vacio' => 'ユーザーがいません。',
        'detalle' => 'ユーザーの詳細',
    ],
];
