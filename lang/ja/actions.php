<?php

declare(strict_types=1);

return [
    // 一般的なアクション
    'back' => '戻る',
    'back_top' => 'トップに戻る',

    // 認証
    'sign_in' => 'サインイン',
    'sign_out' => 'サインアウト',
    'forgot_password' => 'パスワードをお忘れですか？',

    // CRUD - ボタン
    'create' => '作成',

    // フラッシュメッセージ（日本語は性別による変化がないため、両バリアントは同一）
    'created' => '{1} :modelo を作成しました。|{2} :modelo を作成しました。',
    'updated' => '{1} :modelo を更新しました。|{2} :modelo を更新しました。',
    'deleted' => '{1} :modelo を削除しました。|{2} :modelo を削除しました。',
    'restored' => '{1} :modelo を復元しました。|{2} :modelo を復元しました。',

    // 操作に失敗した際の汎用エラーフラッシュ
    'generic_error' => '操作の処理中にエラーが発生しました。もう一度お試しください。',
];
