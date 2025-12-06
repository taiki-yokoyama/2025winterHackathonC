<?php
$email    = $_POST['email']    ?? '';
$username = $_POST['username'] ?? '';

function h($str) {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$errors = [];

// バリデーション（Issue4ですでに追加済み）
if ($email === '') {
  $errors[] = 'メールアドレスを入力してください。';
}
if ($username === '') {
  $errors[] = 'ユーザー名を入力してください。';
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = 'メールアドレスの形式が正しくありません。';
}
if ($username !== '' && mb_strlen($username) > 20) {
  $errors[] = 'ユーザー名は20文字以内で入力してください。';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>登録内容の確認</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-orange-50 flex items-center justify-center">

  <div class="bg-white border-2 border-orange-400 rounded-2xl px-8 py-6 w-[380px]">

    <h1 class="text-center text-orange-500 text-xl font-bold mb-6">
      登録内容の確認
    </h1>

    <?php if (!empty($errors)): ?>
      <!-- 🔴 エラー表示（薄い赤枠） -->
      <div class="border border-red-300 bg-red-50 text-red-600 rounded-lg px-4 py-3 mb-6 text-sm">
        <p class="font-semibold mb-1">入力内容に誤りがあります。</p>
        <ul class="list-disc pl-5">
          <?php foreach ($errors as $e): ?>
            <li><?php echo h($e); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <form action="/signup/index.php" method="get">
        <button class="w-full border border-orange-400 text-orange-500 font-semibold py-2 rounded-full hover:bg-orange-50 transition">
          戻る
        </button>
      </form>

    <?php else: ?>
      <!-- 🟧 アラート風メッセージ -->
      <div class="border border-gray-300 bg-gray-50 text-gray-700 rounded-lg px-4 py-3 mb-4 text-sm">
        この情報で登録してもよろしいですか？
      </div>

      <!-- 入力内容の表示 -->
      <div class="space-y-3 mb-6">
        <div>
          <p class="text-xs text-gray-500">メールアドレス</p>
          <input type="text" value="<?php echo h($email); ?>" readonly
            class="w-full p-2 bg-gray-100 rounded border border-gray-300 text-sm">
        </div>

        <div>
          <p class="text-xs text-gray-500">ユーザー名</p>
          <input type="text" value="<?php echo h($username); ?>" readonly
            class="w-full p-2 bg-gray-100 rounded border border-gray-300 text-sm">
        </div>
      </div>

      <!-- ボタン横並び -->
      <div class="flex gap-4">
        <form action="/signup/index.php" method="get" class="flex-1">
          <button class="w-full border border-orange-400 text-orange-500 py-2 rounded-full font-semibold hover:bg-orange-50 transition">
            キャンセル
          </button>
        </form>

        <form action="/signup/complete.php" method="post" class="flex-1">
          <input type="hidden" name="email" value="<?php echo h($email); ?>">
          <input type="hidden" name="username" value="<?php echo h($username); ?>">
          <button class="w-full bg-orange-400 text-white py-2 rounded-full font-semibold hover:bg-orange-500 transition">
            登録
          </button>
        </form>
      </div>

    <?php endif; ?>

  </div>

</body>
</html>
