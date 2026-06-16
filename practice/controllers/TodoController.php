<?php
class TodoController {
    private $todoModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $this->todoModel = new Todo();
    }

    public function handle($action) {
        $user_id = $_SESSION['user_id'];

        switch ($action) {
            case 'todo':
                // 一覧を取得
                $todos = $this->todoModel->getByUserId($user_id);
                
                // URLに &detail_id=X があれば、そのタスクの詳細を取得
                $detailTodo = null;
                $detail_id = $_GET['detail_id'] ?? null;
                if ($detail_id) {
                    $detailTodo = $this->todoModel->getById($detail_id, $user_id);
                }

                require_once __DIR__ . '/../views/todo/index.php';
                break;

            case 'todo_add':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $title = trim($_POST['title'] ?? '');
                    $content = trim($_POST['content'] ?? ''); // 詳細内容を取得
                    $this->todoModel->add($user_id, $title, $content);
                }
                header("Location: index.php?action=todo");
                exit;

            case 'todo_toggle':
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $this->todoModel->toggle($id, $user_id);
                }
                // 完了ボタンを押した後も詳細画面を開いたままにするためリダイレクト先を調整
                header("Location: index.php?action=todo&detail_id=" . $id);
                exit;

            case 'todo_delete':
                $id = $_GET['id'] ?? null;
                if ($id) {
                    $this->todoModel->delete($id, $user_id);
                }
                header("Location: index.php?action=todo");
                exit;
        }
    }
}