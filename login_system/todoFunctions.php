<?php
// すでに functions.php が読み込まれている前提ですが、
// 安全のために db_connect() が存在するか確認しながら定義します。
require_once 'functions.php';

/**
 * ログインユーザーのタスク一覧を取得する関数
 */
function get_tasks($user_id) {
    $pdo = db_connect();
    // 他人のタスクが見えないよう、WHERE user_id = :user_id で厳格に絞り込みます。
    // 作成日時の新しい順（DESC）で取得します。
    $sql = "SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll();
}

/**
 * 新しいタスクを追加する関数
 */
function add_task($user_id, $title, $body) {
    $pdo = db_connect();
    // プリペアドステートメントを使い、SQLインジェクションを完全に防御します。
    $sql = "INSERT INTO tasks (user_id, title, body) VALUES (:user_id, :title, :body)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':user_id' => $user_id,
        ':title'   => $title,
        ':body'    => $body
    ]);
}

/**
 * タスクの完了／未完了（complete）の状態を切り替える関数
 */
function toggle_task_status($user_id, $task_id) {
    $pdo = db_connect();
    
    // 【重要】不正操作対策。
    // 変更しようとしているタスクが、本当にログインしているユーザーのものか（user_idの一致）を必ず検証します。
    $sql = "SELECT complete FROM tasks WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $task_id, ':user_id' => $user_id]);
    $task = $stmt->fetch();
    
    if ($task) {
        // 現在の状態を反転させる（0なら1、1なら0）
        $new_status = $task['complete'] == 1 ? 0 : 1;
        
        $update_sql = "UPDATE tasks SET complete = :complete WHERE id = :id AND user_id = :user_id";
        $update_stmt = $pdo->prepare($update_sql);
        return $update_stmt->execute([
            ':complete' => $new_status,
            ':id'       => $task_id,
            ':user_id'  => $user_id
        ]);
    }
    return false;
}