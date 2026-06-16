<?php
require_once 'functions.php';

// すでにログインしている場合は、再度ログインさせる必要がないためマイページへ。
if (is_logged_in()) {
    header('Location: mypage.php');
    exit;
}

$errors = [];
$email = '';

// フォームがPOST送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // ログイン処理関数の呼び出し
    if (login_user($email, $password)) {
        // 認証成功ならマイページへリダイレクト
        header('Location: mypage.php');
        exit;
    } else {
        // 【セキュリティ対策】「パスワードが違います」等と詳細に書くと、登録されているメールアドレスの一致確認（アカウント列挙攻撃）に悪用されるため、
        // あえてどちらが間違っているか分からない「曖昧で安全なメッセージ」にします。
        $errors[] = 'メールアドレスまたはパスワードが正しくありません。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>
<body>
    <h2>ログイン</h2>

    <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
        <p style="color: green; font-weight: bold;">登録完了しました。ログインしてください。</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo h($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div>
            <label for="email">メールアドレス：</label>
            <input type="email" name="email" id="email" value="<?php echo h($email); ?>" required>
        </div>
        <div>
            <label for="password">パスワード：</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">ログイン</button>
    </form>
    <p><a href="signup.php">新規登録はこちら</a></p>
</body>
</html>