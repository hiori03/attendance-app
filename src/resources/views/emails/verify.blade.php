@component('mail::message')
# メールアドレス確認

こんにちは {{ $user->name }} さん。

下のボタンをクリックして、メールアドレス認証を完了してください。

@component('mail::button', ['url' => $url])
メールアドレスを確認する
@endcomponent

このリンクは60分後に期限切れになります。

@component('mail::subcopy')
もしボタンがクリックできない場合は、次のURLをブラウザにコピーして開いてください：
{{ $url }}
@endcomponent

@endcomponent