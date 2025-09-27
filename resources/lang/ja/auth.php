<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => '認証情報が記録と一致しません。',
    'throttle' => 'ログイン試行回数が多すぎます。:seconds 秒後にお試しください。',
    'banned' => 'アカウントが管理者によって禁止されています。',
    'approval' => 'アカウントが管理者の承認待ちです。',
    'max_sessions_reached' => '許可されている最大アクティブセッション数に達しました。他のデバイスでログアウトしてから、もう一度お試しください。',

    '2fa' => [
        'enabled_successfully' => '二要素認証が正常に有効化されました。',
        'disabled_successfully' => '二要素認証が正常に無効化されました。',
        'already_enabled' => 'このユーザーに対して2FAは既に有効化されています。',
        'not_enabled' => 'このユーザーに対して2FAは有効化されていません。',
        'phone_in_use' => '指定された電話番号と国コードを持つユーザーが既に存在します。',
        'invalid_token' => '無効な2FAトークンです。',
        'token_sent' => '認証トークンが送信されました。',
    ],
];
