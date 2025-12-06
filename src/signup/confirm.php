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

    <p class="text-sm text-gray-700 mb-4">
      この情報で登録してもよろしいですか？
      <br>（Issue3で入力値を渡す予定）
    </p>

    <div class="flex justify-between gap-3 mt-4">
      <a href="/signup/index.php"
         class="flex-1 text-center border border-orange-400 text-orange-500 rounded-full py-2 text-sm">
        戻る
      </a>

      <a href="/signup/complete.php"
         class="flex-1 text-center bg-orange-400 text-white rounded-full py-2 text-sm">
        登録
      </a>
    </div>
  </div>
</body>
</html>
