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
    'ordenacion' => [
        'atributo' => '並べ替え',
        'atributo_direccion' => '並べ替え方向',
        'ordenar_ascendente' => ':columnaを昇順で並べ替え',
        'ordenar_descendente' => ':columnaを降順で並べ替え',
        'quitar_ordenacion' => ':columnaの並べ替えを解除',
    ],
    'usuarios' => [
        'titulo' => 'ユーザー',
        'vacio' => 'ユーザーがいません。',
        'detalle' => 'ユーザーの詳細',
        'datos' => 'ユーザー情報',
        'cambio_password' => 'パスワードの変更',
        'password_opcional_aviso' => 'パスワードを入力しない場合、ランダムなパスワードが生成されます。ユーザーは「パスワードをお忘れですか」から届くメールのメッセージでパスワードを変更する必要があります。',
    ],
];
