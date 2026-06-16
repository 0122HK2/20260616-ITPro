<?php require_once __DIR__ . '/../layout/header.php'; ?>

<?php
/**
 * エディタの未定義警告を消すための記述（コメントなので動作には影響しません）
 * @var array|null $detailTodo 
 * @var array $todos
 */
?>

<h2>TODOリスト（マイページ）</h2>

<form action="index.php?action=todo_add" method="POST" style="margin-bottom: 30px; background: #f9f9f9; padding: 15px; border-radius: 4px;">
    <div class="form-group">
        <label for="title">タスク名（必須）</label>
        <input type="text" name="title" id="title" placeholder="やることを入力..." required>
    </div>
    <div class="form-group" style="margin-top: 10px;">
        <label for="content">タスク内容・詳細</label>
        <textarea name="content" id="content" rows="3" style="width:100%; box-sizing:border-box; padding:8px; border:1px solid #ddd; border-radius:4px;"></textarea>
    </div>
    <button type="submit" style="margin-top: 10px;">タスクを追加</button>
</form>

<hr>

<div style="display: flex; gap: 30px; margin-top: 20px;">

    <div style="flex: 1;">
        <h3>タスク一覧</h3>
        <ul class="todo-list">
            <?php if (empty($todos)): ?>
                <li>タスクはまだありません。</li>
            <?php else: ?>
                <?php foreach ($todos as $todo): ?>
                    <li class="todo-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
                        <span class="<?= $todo['is_completed'] ? 'completed' : '' ?>">
                            <?= h($todo['title']) ?>
                        </span>
                        <a href="index.php?action=todo&detail_id=<?= $todo['id'] ?>">
                            <button type="button" style="background-color: #6c757d;">詳細</button>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div style="flex: 1; background: #fff; border: 1px solid #dee2e6; padding: 20px; border-radius: 6px; min-height: 200px;">
        <?php if ($detailTodo): ?>
            <h3>【詳細】<?= h($detailTodo['title']) ?></h3>
            <p style="white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 4px; min-height: 80px; color: #555;">
                <?= $detailTodo['content'] ? h($detailTodo['content']) : '（内容はありません）' ?>
            </p>
            
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <a href="index.php?action=todo_toggle&id=<?= $detailTodo['id'] ?>">
                    <button class="btn-success"><?= $detailTodo['is_completed'] ? '未完了に戻す' : '完了にする' ?></button>
                </a>
                <a href="index.php?action=todo_delete&id=<?= $detailTodo['id'] ?>" onclick="return confirm('本当にこのタスクを削除しますか？')">
                    <button class="btn-danger">削除</button>
                </a>
            </div>
        <?php else: ?>
            <p style="color: #888; text-align: center; margin-top: 60px;">左の一覧から「詳細」ボタンを押すと、ここにタスク内容が表示されます。</p>
        <?php endif; ?>
    </div>

</div>

</div>
</body>
</html>