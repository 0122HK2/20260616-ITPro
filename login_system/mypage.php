<?php
require_once 'functions.php';
require_once 'todoFunctions.php'; // TODO用の関数ファイルを読み込み

// 未ログインユーザーのアクセスを完全に遮断
require_login();

// ログイン中のユーザーIDを取得（タイピングを減らす＆可読性向上のため変数に代入）
$current_user_id = $_SESSION['user_id'];

$errors = [];

// ==========================================
// フォームからデータがPOST送信された場合の処理
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ① タスク追加処理
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $body  = isset($_POST['body']) ? trim($_POST['body']) : '';
        
        // バリデーション：タイトルは必須
        if ($title === '') {
            $errors[] = 'タスクのタイトルを入力してください。';
        }
        
        if (empty($errors)) {
            // 安全に関数を呼び出してタスクを登録
            add_task($current_user_id, $title, $body);
            // 画面の再読み込み（F5）による二重投稿を防ぐため、自分自身にリダイレクトします（POST-Redirect-GETパターン）
            header('Location: mypage.php');
            exit;
        }
    }
    
    // ② タスクの状態切り替え（完了／未完了）処理
    if (isset($_POST['action']) && $_POST['action'] === 'toggle') {
        $task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;
        
        if ($task_id > 0) {
            toggle_task_status($current_user_id, $task_id);
            header('Location: mypage.php');
            exit;
        }
    }
}

// 画面に表示するためのタスク一覧をデータベースから取得
$tasks = get_tasks($current_user_id);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>マイページ 兼 TODOアプリ</title>
    <style>
        .todo-list { list-style: none; padding: 0; }
        .todo-item { border-bottom: 1px solid #ccc; padding: 10px 0; display: flex; justify-content: space-between; align-items: center; }
        .completed .title { text-decoration: line-through; color: #888; }
        .completed .body { text-decoration: line-through; color: #aaa; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>マイページ（TODO機能付き）</h2>
    <p>ようこそ、<?php echo h($_SESSION['email']); ?> さん（ユーザーID: <?php echo h($current_user_id); ?>）</p>
    <p><a href="logout.php">ログアウトする</a></p>
    
    <hr>

    <h3>新規タスクの追加</h3>
    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $error): ?>
                <li><?php echo h($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="mypage.php" method="POST">
        <input type="hidden" name="action" value="add">
        <div>
            <label for="title">タイトル（必須）：</label><br>
            <input type="text" name="title" id="title" required style="width: 300px;">
        </div>
        <div style="margin-top: 10px;">
            <label for="body">詳細（任意）：</label><br>
            <textarea name="body" id="body" rows="3" style="width: 300px;"></textarea>
        </div>
        <button type="submit" style="margin-top: 10px;">タスクを追加</button>
    </form>

    <hr>

    <h3>あなたのタスク一覧</h3>
    <?php if (empty($tasks)): ?>
        <p>タスクはまだありません。</p>
    <?php else: ?>
        <ul class="todo-list">
            <?php foreach ($tasks as $task): ?>
                <li class="todo-item <?php echo $task['complete'] == 1 ? 'completed' : ''; ?>">
                    <div>
                        <strong class="title"><?php echo h($task['title']); ?></strong>
                        <?php if ($task['body'] !== ''): ?>
                            <br><span class="body" style="font-size: 0.9em;"><?php echo nl2br(h($task['body'])); ?></span>
                        <?php endif; ?>
                        <br><small style="color:#999;">作成日: <?php echo h($task['created_at']); ?></small>
                    </div>
                    <div>
                        <form action="mypage.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="task_id" value="<?php echo (int)$task['id']; ?>">
                            <button type="submit">
                                <?php echo $task['complete'] == 1 ? '未完了に戻す' : '完了にする'; ?>
                            </button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>