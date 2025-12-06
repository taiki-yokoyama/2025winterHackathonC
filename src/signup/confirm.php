<?php
// フォームから送られてきた値を受け取る
$email    = $_POST['email']    ?? '';
$username = $_POST['username'] ?? '';

// XSS対策
function h($str) {
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// バリデーション
$errors = [];

// 空チェック
if ($email === '') {
  $errors[] = 'メールアドレスを入力してください。';
}
if ($username === '') {
  $errors[] = 'ユーザー名を入力してください。';
}

// メール形式チェック
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = 'メールアドレスの形式が正しくありません。';
}

// ユーザー名の長さチェック
if ($username !== '' && mb_strlen($username) > 20) {
  $errors[] = 'ユーザー名は20文字以内で入力してください。';
}

// 完全に何もない状態で来たら新規登録に戻す
if ($email === '' && $username === '') {
  header('Location: /signup/index.php');
  exit;
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

  <div class="bg-white border-2 border-orange-400 rounded-2xl px-8 py-6 w-[360px]">
    <h1 class="text-center text-orange-500 text-xl font-bold mb-6">
      登録内容の確認
    </h1>

    <?php if (!empty($errors)): ?>
      <!-- エラーがあるときの表示 -->
      <div class="border border-red-300 bg-red-50 text-red-700 rounded-lg px-4 py-3 mb-4 text-sm">
        <p class="font-semibold mb-1">入力内容に誤りがあります。</p>
        <ul class="list-disc list-inside">
          <?php foreach ($errors as $error): ?>
            <li><?php echo h($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <form action="/signup/index.php" method="get" class="mt-2">
        <button
          type="submit"
          class="w-full border border-orange-400 text-orange-500 rounded-full py-2 text-sm font-semibold hover:bg-orange-50 transition"
        >
          戻る
        </button>
      </form>

    <?php else: ?>
      <!-- 問題ないときは確認表示 -->
      <div class="border border-gray-300 rounded-lg px-4 py-3 mb-4 text-sm text-gray-700">
        この情報で登録してもよろしいですか？
      </div>

      <!-- 入力内容の表示 -->
      <div class="space-y-2 mb-6 text-sm">
        <div>
          <span class="font-semibold text-gray-700">メールアドレス：</span>
          <span><?php echo h($email); ?></span>
        </div>
        <div>
          <span class="font-semibold text-gray-700">ユーザー名：</span>
          <span><?php echo h($username); ?></span>
        </div>
      </div>

      <div class="flex justify-between gap-3">
        <!-- 戻る：新規登録に戻る -->
        <form action="/signup/index.php" method="get" class="flex-1">
          <button
            type="submit"
            class="w-full border border-orange-400 text-orange-500 rounded-full py-2 text-sm font-semibold hover:bg-orange-50 transition"
          >
            戻る
          </button>
        </form>

        <!-- 登録：完了画面へ進む -->
        <form action="/signup/complete.php" method="post" class="flex-1">
          <input type="hidden" name="email" value="<?php echo h($email); ?>">
          <input type="hidden" name="username" value="<?php echo h($username); ?>">
          <button
            type="submit"
            class="w-full bg-orange-400 text-white rounded-full py-2 text-sm font-semibold hover:bg-orange-500 transition"
          >
            登録
          </button>
        </form>
      </div>
    <?php endif; ?>

  </div>

</body>
</html>
