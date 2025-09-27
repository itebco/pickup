<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute は承認される必要があります。',
    'active_url' => ':attribute は有効なURLではありません。',
    'after' => ':attribute は :date より後の日付である必要があります。',
    'alpha' => ':attribute は文字のみを含むことができます。',
    'alpha_dash' => ':attribute は文字、数字、ダッシュのみを含むことができます。',
    'alpha_num' => ':attribute は文字と数字のみを含むことができます。',
    'array' => ':attribute は配列である必要があります。',
    'before' => ':attribute は :date より前の日付である必要があります。',
    'between' => [
        'numeric' => ':attribute は :min から :max の間である必要があります。',
        'file' => ':attribute は :min から :max キロバイトの間である必要があります。',
        'string' => ':attribute は :min から :max 文字の間である必要があります。',
        'array' => ':attribute は :min から :max 個の項目を持つ必要があります。',
    ],
    'boolean' => ':attribute フィールドは true または false である必要があります。',
    'confirmed' => ':attribute の確認が一致しません。',
    'date' => ':attribute は有効な日付ではありません。',
    'date_format' => ':attribute は :format 形式と一致しません。',
    'different' => ':attribute と :other は異なる必要があります。',
    'digits' => ':attribute は :digits 桁である必要があります。',
    'digits_between' => ':attribute は :min から :max 桁の間である必要があります。',
    'email' => ':attribute は有効なメールアドレスである必要があります。',
    'exists' => '選択された :attribute は無効です。',
    'filled' => ':attribute フィールドは必須です。',
    'image' => ':attribute は画像である必要があります。',
    'in' => '選択された :attribute は無効です。',
    'integer' => ':attribute は整数である必要があります。',
    'ip' => ':attribute は有効なIPアドレスである必要があります。',
    'json' => ':attribute は有効なJSON文字列である必要があります。',
    'max' => [
        'numeric' => ':attribute は :max より大きくできません。',
        'file' => ':attribute は :max キロバイトより大きくできません。',
        'string' => ':attribute は :max 文字より大きくできません。',
        'array' => ':attribute は :max 個より多くの項目を持つことはできません。',
    ],
    'mimes' => ':attribute は :values タイプのファイルである必要があります。',
    'min' => [
        'numeric' => ':attribute は少なくとも :min である必要があります。',
        'file' => ':attribute は少なくとも :min キロバイトである必要があります。',
        'string' => ':attribute は少なくとも :min 文字である必要があります。',
        'array' => ':attribute は少なくとも :min 個の項目を持つ必要があります。',
    ],
    'not_in' => '選択された :attribute は無効です。',
    'numeric' => ':attribute は数字である必要があります。',
    'regex' => ':attribute の形式が無効です。',
    'required' => ':attribute フィールドは必須です。',
    'required_if' => ':other が :value の場合、:attribute フィールドは必須です。',
    'required_with' => ':values が存在する場合、:attribute フィールドは必須です。',
    'required_with_all' => ':values が存在する場合、:attribute フィールドは必須です。',
    'required_without' => ':values が存在しない場合、:attribute フィールドは必須です。',
    'required_without_all' => ':values がいずれも存在しない場合、:attribute フィールドは必須です。',
    'same' => ':attribute と :other は一致する必要があります。',
    'size' => [
        'numeric' => ':attribute は :size である必要があります。',
        'file' => ':attribute は :size キロバイトである必要があります。',
        'string' => ':attribute は :size 文字である必要があります。',
        'array' => ':attribute は :size 個の項目を含む必要があります。',
    ],
    'string' => ':attribute は文字列である必要があります。',
    'timezone' => ':attribute は有効なゾーンである必要があります。',
    'unique' => ':attribute は既に使用されています。',
    'url' => ':attribute の形式が無効です。',
    'captcha' => 'reCAPTCHAの値が無効です。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
