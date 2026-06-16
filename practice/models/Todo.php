<?php
class Todo {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // 特定ユーザーのTODO一覧を取得（変更なし）
    public function getByUserId($user_id) {
        $sql = "SELECT * FROM todos WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    // TODOの追加（content を追加）
    public function add($user_id, $title, $content) {
        if (empty($title)) return false;
        $sql = "INSERT INTO todos (user_id, title, content) VALUES (:user_id, :title, :content)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':content' => $content
        ]);
    }

    // 単一のTODO詳細を取得（詳細表示用に追加）
    public function getById($id, $user_id) {
        $sql = "SELECT * FROM todos WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id, ':user_id' => $user_id]);
        return $stmt->fetch();
    }

    // 完了・未完了の切り替え（変更なし）
    public function toggle($id, $user_id) {
        $sql = "UPDATE todos SET is_completed = NOT is_completed WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id
        ]);
    }

    // TODOの削除（変更なし）
    public function delete($id, $user_id) {
        $sql = "DELETE FROM todos WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $user_id
        ]);
    }
}