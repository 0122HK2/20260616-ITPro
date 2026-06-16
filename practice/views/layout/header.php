<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>MVC ログイン＆TODO管理システム</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"], input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .error { color: red; margin-bottom: 15px; }
        .nav { margin-bottom: 20px; text-align: right; }
        .todo-list { list-style: none; padding: 0; }
        .todo-item { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; align-items: center; }
        .completed { text-decoration: line-through; color: #888; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #bd2130; }
        .btn-success { background-color: #28a745; }
        .btn-success:hover { background-color: #218838; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <?php if (isset($_SESSION['user_id'])): ?>
            ログイン中: <strong><?= h($_SESSION['email']) ?></strong> | 
            <a href="index.php?action=logout">ログアウト</a>
        <?php else: ?>
            <a href="index.php?action=login">ログイン</a> | 
            <a href="index.php?action=register">新規登録</a>
        <?php endif; ?>
    </div>