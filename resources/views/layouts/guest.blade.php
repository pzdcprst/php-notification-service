<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Вход') · Лист</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="auth-page">
        <aside class="auth-aside">
            <a class="brand" href="{{ route('login') }}"><span class="brand-mark">Л</span><span class="brand-name">Лист<span class="brand-caption">личная записная книжка</span></span></a>
            <div class="auth-note">
                <p class="eyebrow">Для людей и планов</p>
                <h1>Не упустить важное.</h1>
                <p>Контакты, события и напоминания, собранные в одной спокойной книжке.</p>
            </div>
            <p class="auth-footer">ЛИСТ · ВАША ЛИЧНАЯ КНИЖКА</p>
        </aside>
        <section class="auth-main">
            @yield('content')
        </section>
    </main>
</body>
</html>