<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log in · Underlord</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Grenze+Gotisch:wght@500;700&family=Alegreya+Sans:wght@400;500;700&display=swap">
  @vite(['resources/css/underlord.css'])
</head>
<body class="page">
  <main class="login">
    <h1>Underlord</h1>
    <p>Log in to save your dungeon to your account. Without an account your games are saved in this browser only.</p>
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">
      <label class="check"><input type="checkbox" name="remember" value="1"> Keep me logged in</label>
      @error('email')<p class="error">{{ $message }}</p>@enderror
      <button type="submit" class="main">Log in</button>
    </form>
    <a href="{{ route('game') }}">Back to the game</a>
  </main>
</body>
</html>
