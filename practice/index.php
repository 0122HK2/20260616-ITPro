<?php
// セッションの開始（セキュリティ向上のためCookieの設定を指定）
session_start([
    'cookie_lifetime' => 0,
    'cookie_path' => '/',
    'cookie_secure' => false, // HTTPS環境なら true に変更
    'cookie_httponly' => true, // JavaScriptからのセッションID読み取りを防御
    'cookie_samesite' => 'Lax'
]);

// セキュリティ対策（XSS対策用ヘルパー関数）
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// ルーティング用パラメータの取得（デフォルトはログイン画面）
$action = $_GET['action'] ?? 'login';

// 必要なコントローラーの読み込み
// ※ステップ2のファイルもここでまとめて読み込みます
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Todo.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/TodoController.php';

// アクションに応じた振り分け（共通ルーティング）
switch ($action) {
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    case 'todo':
    case 'todo_add':
    case 'todo_toggle':
    case 'todo_delete':
        $controller = new TodoController();
        $controller->handle($action);
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        echo "ページが見つかりません。";
        break;
}