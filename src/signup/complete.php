<?php
require_once __DIR__ . '/../common/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./index.php');
    exit;
}

$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';

try {
    $pdo = getPdo();

    $stmt = $pdo->prepare(
        'INSERT INTO users (email, username) VALUES (:email, :username)'
    );
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $message = '登録が完了しました。';

} catch (PDOException $e) {
    $message = 'エラーが発生しました：' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>登録完了</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-orange-50 flex items-center justify-center">

  <div class="bg-white border-2 border-orange-400 rounded-2xl px-8 py-6 w-[360px] text-center">

    <h1 class="text-orange-500 text-xl font-bold mb-4">
      登録が完了しました。
    </h1>

    <?php if (!empty($message) && isset($e)) : ?>
    <p class="text-center text-orange-600 font-bold text-xl mt-4">
      <?= htmlspecialchars($message) ?>
    </p>
    <?php endif; ?>


    <p class="text-sm text-gray-700 mb-6">
      ログインしてサービスを開始できます。
    </p>

    <!-- ログイン画面へのボタン（1つだけ） -->
    <a
      href="/login/index.php"
      class="inline-flex items-center justify-center bg-orange-400 text-white px-6 py-2 rounded-full text-sm font-semibold hover:bg-orange-500 transition"
    >
      ログイン画面へ
    </a>

  </div>

</body>
</html>
