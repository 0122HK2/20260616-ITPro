<?php
require_once 'functions.php';

// 1. セッション変数をすべて空の配列にして初期化します（メモリ上のセッションデータを消去）。
$_SESSION = [];

// 2. ブラウザ側に残っている「セッションコクッキー（Session IDが入ったクッキー）」を明示的に削除します。
// クッキーの有効期限を過去（time() - 42000）に設定することで、ブラウザに強制廃棄させます。
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 3. サーバー側に保存されているセッションファイル（実態）を完全に破棄します。
session_destroy();

// 4. ログアウト完了後、安全にログイン画面へリダイレクトします。
header('Location: login.php');
exit; // 最後まで処理の終了を徹底します。