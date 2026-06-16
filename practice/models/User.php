<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // 新規ユーザー登録
    public function create($email, $password) {
        // パスワードのハッシュ化（必須のセキュリティ対策）
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':email' => $email,
            ':password' => $hashed_password
        ]);
    }

    // メールアドレスからユーザーを取得
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
}