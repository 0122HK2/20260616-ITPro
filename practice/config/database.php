<?php
class Database {
    private static $host = 'localhost';
    private static $db_name = 'it_20260614_db';
    private static $username = 'root'; // XAMPPのデフォルト値
    private static $password = '';     // XAMPPのデフォルト値
    private static $conn = null;

    public static function connect() {
        if (self::$conn === null) {
            try {
                // SQLインジェクション対策・エラーハンドリングのためのオプション設定
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,         // 例外をスロー
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // 連想配列で取得
                    PDO::ATTR_EMULATE_PREPARES => false,                // 静的プレースホルダを強制
                ];

                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4",
                    self::$username,
                    self::$password,
                    $options
                );
            } catch (PDOException $e) {
                // 本番環境ではエラーメッセージをそのまま出さないこと（セキュリティ対策）
                die("データベース接続エラー: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}