<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h1 class="text-3xl font-bold">Admin Dashboard</h1>
  <form method="POST" action="{{ route('auth.logout') }}">
      @csrf
      <button class="bg-red-500 text-white px-4 py-2 mt-4">Logout</button>
  </form>
</body>
</html>