<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>新規登録</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-orange-50 flex items-center justify-center">

  <div class="bg-white border-2 border-orange-400 rounded-2xl px-8 py-6 w-[360px]">

    <h1 class="text-center text-orange-500 text-xl font-bold mb-6">
      新規登録
    </h1>

    <form action="/signup/confirm.php" method="POST" class="space-y-4">

      <!-- メールアドレス -->
      <div>
        <label class="text-sm text-orange-500 font-semibold">メールアドレス</label>
        <input
          type="email"
          name="email"
          required
          class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-400"
        >
      </div>

      <!-- ユーザー名 -->
      <div>
        <label class="text-sm text-orange-500 font-semibold">ユーザー名</label>
        <input
          type="text"
          name="username"
          required
          class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-orange-400"
        >
      </div>

      <!-- ボタン -->
      <div class="text-right pt-2">
        <button
          type="submit"
          class="bg-orange-400 text-white px-5 py-2 rounded-full font-semibold hover:bg-orange-500 transition"
        >
          登録
        </button>
      </div>
    </form>

  </div>

</body>
</html>
