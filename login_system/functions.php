<?php
// ==========================================
// 1. 定数の定義（データベース接続情報）
// ==========================================
// 接続情報を定数化することで、後からの変更を一箇所で管理できるようにします。
define('DB_HOST', 'localhost');
define('DB_NAME', 'it_20260616_db');
define('DB_USER', 'root'); // 環境に合わせて変更してください
define('DB_PASS', '');     // 環境に合わせて変更してください
define('DB_CHAR', 'utf8mb4'); // 文字化けやSQLインジェクション脆弱性を防ぐため、utf8mb4を指定

// ==========================================
// 2. セッションの自動開始
// ==========================================
// ログイン状態の保持にはセッションが必須です。
// すでに開始されているか確認（PHP_SESSION_NONE）し、未開始の場合のみsession_start()を呼び出します。
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// 3. 共通関数の定義
// ==========================================

/**
 * ① db_connect: PDOを用いてDBに安全に接続し、接続オブジェクトを返す関数
 */
function db_connect() {
    // DSN（Data Source Name）の設定。接続先や文字コードを指定します。
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHAR;
    
    // PDOのオプション設定
    $options = [
        // エラー発生時に例外（Exception）をスローする設定。不具合にすぐ気づけるようになります。
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // データの取得モードを連想配列（キー名がカラム名）に固定します。
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // SQLインジェクション対策として、PDO側でのエミュレートをOFFにし、MySQL本来のプリペアドステートメントを使用します。
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        // データベースに接続を試みます。
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // 接続エラー時は、生の接続情報を画面に出すとセキュリティリスクになるため、
        // メッセージを隠して処理を安全に停止します。
        exit('データベース接続に失敗しました。');
    }
}

/**
 * ⑥ h: XSS（クロスサイトスクリプティング）対策用のエスケープ関数
 * ※ 処理の順番前後しますが、下部で使うためここで定義します。
 */
function h($str) {
    // ユーザーが入力したHTMLタグやJavaScript（<script>など）の効果を無効化（無害な文字列に変換）します。
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * ② signup_user: ユーザーを安全にデータベースに登録する関数
 */
function signup_user($email, $password) {
    $pdo = db_connect(); // データベース接続を取得

    // SQLインジェクションを防ぐため、値を直接入れずプレースホルダ（:email等）を用いたSQLを用意します。
    $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
    
    // 万が一データベースが漏洩してもパスワードが生のまま流出しないよう、強力なハッシュアルゴリズムで暗号化します。
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare($sql); // SQLの実行準備（プリペアドステートメント）
        // プレースホルダに実際の安全な値をバインドして実行します。
        $stmt->execute([
            ':email' => $email,
            ':password' => $hashed_password
        ]);
        return true; // 登録成功
    } catch (PDOException $e) {
        // メールアドレスの重複（UNIQUE制約違反）などが起きた場合は false を返します。
        return false; 
    }
}

/**
 * ③ login_user: メールアドレスとパスワードを検証し、ログイン処理を行う関数
 */
function login_user($email, $password) {
    $pdo = db_connect();

    // 入力されたメールアドレスを持つユーザーを検索するSQL
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(); // 該当するユーザーデータを1件取得

    // ユーザーが存在し、かつ入力されたパスワードがハッシュ値と一致するか検証します。
    if ($user && password_verify($password, $user['password'])) {
        // 【重要】セッションハイジャック対策。ログイン成功時にセッションIDを新しく作り直します（古いIDを破棄）。
        session_regenerate_id(true);
        
        // セッション変数にユーザー情報を保存し、ログイン状態にします。
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        return true; // 認証成功
    }

    return false; // 認証失敗（メールアドレスが存在しない、またはパスワード間違い）
}

/**
 * ④ is_logged_in: 現在ログインしているかをチェックする関数
 */
function is_logged_in() {
    // セッションに 'user_id' が存在していればログイン中（true）、なければ未ログイン（false）と判定します。
    return isset($_SESSION['user_id']);
}

/**
 * ⑤ require_login: 未ログインユーザーを制限し、強制リダイレクトする関数
 */
function require_login() {
    // もしログインしていなければ
    if (!is_logged_in()) {
        // ログインページへリダイレクトを指示するヘッダーを送信
        header('Location: login.php');
        // 【重要】リダイレクトの指示を出しても、後ろのプログラムはそのまま実行されてしまいます。
        // 情報漏洩を防ぐため、exitで完全に処理を終了させます。
        exit;
    }
}