<?php
class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // ユーザー登録処理
    public function register() {
        $error = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "すべての項目を入力してください。";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "正しいメールアドレスの形式で入力してください。";
            } elseif ($this->userModel->findByEmail($email)) {
                $error = "このメールアドレスは既に登録されています。";
            } else {
                if ($this->userModel->create($email, $password)) {
                    // 登録成功したらログイン画面へ
                    header("Location: index.php?action=login");
                    exit;
                } else {
                    $error = "登録に失敗しました。";
                }
            }
        }
        require_once __DIR__ . '/../views/user/register.php';
    }

    // ログイン処理
    public function login() {
        $error = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            // ユーザーが存在し、パスワードが一致するか検証
            if ($user && password_verify($password, $user['password'])) {
                // セッション固定攻撃（Session Fixation）対策
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];

                header("Location: index.php?action=todo");
                exit;
            } else {
                $error = "メールアドレスまたはパスワードが正しくありません。";
            }
        }
        require_once __DIR__ . '/../views/user/login.php';
    }

    // ログアウト処理
    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}