<?php
// 共通関数ファイルを読み込みます。これ以降、定義した関数や定数がすべて使えます。
require_once 'functions.php';

// すでにログインしている場合は、新規登録する必要がないためマイページへ自動遷移させます。
if (is_logged_in()) {
    header('Location: mypage.php');
    exit; // リダイレクト後の処理中断（鉄則です）
}

// エラーメッセージと入力保持用変数の初期化
$errors = [];
$email = '';

// フォームがPOST送信された場合のみ登録処理を実行します。
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ユーザー入力を取得。未入力の場合は空文字を設定
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // 【バリデーション（入力チェック）】
    // PHP標準のフィルタを使い、正しいメールアドレスの形式かチェックします。
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '適切なメールアドレスの形式で入力してください。';
    }
    // パスワードの安全性を高めるため、最低文字数をチェックします（今回は6文字以上）。
    if (strlen($password) < 6) {
        $errors[] = 'パスワードは6文字以上で入力してください。';
    }

    // エラーが一件もない場合、データベースへの登録を試みます。
    if (empty($errors)) {
        // functions.phpで定義した登録関数を呼び出し
        if (signup_user($email, $password)) {
            // 登録成功時は、パラメータに「?signup=success」を付与してログイン画面へ。
            header('Location: login.php?signup=success');
            exit;
        } else {
            // UNIQUE制約などで失敗した場合のエラー
            $errors[] = 'このメールアドレスは既に登録されています。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規ユーザー登録</title>
</head>
<body>
    <h2>新規ユーザー登録</h2>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo h($error); ?></li> <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="signup.php" method="POST">
        <div>
            <label for="email">メールアドレス：</label>
            <input type="email" name="email" id="email" value="<?php echo h($email); ?>" required>
        </div>
        <div>
            <label for="password">パスワード（6文字以上）：</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">登録する</button>
    </form>
    <p><a href="login.php">ログインはこちら</a></p>
</body>
</html>