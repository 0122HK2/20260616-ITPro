<?php require_once __DIR__ . '/../layout/header.php'; ?>

<h2>ログイン</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= h($error) ?></div>
<?php endif; ?>

<form action="index.php?action=login" method="POST">
    <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" required>
    </div>
    <div class="form-group">
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>
    </div>
    <button type="submit">ログイン</button>
</form>

</div>
</body>
</html>