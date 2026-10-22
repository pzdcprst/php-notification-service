<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Записная книжка') · Лист</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="mobile-bar">
        <a class="mobile-brand" href="{{ route('dashboard') }}"><span class="brand-mark">Л</span> Лист</a>
        <button class="menu-button" type="button" data-menu-toggle aria-label="Открыть меню" aria-expanded="false">Меню</button>
    </div>
    <div class="app-shell">
        <aside class="sidebar" data-sidebar>
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark">Л</span>
                <span class="brand-name">Лист<span class="brand-caption">личная записная книжка</span></span>
            </a>
            <p class="nav-label">Разделы</p>
            <nav class="nav-list" aria-label="Основная навигация">
                @php($navigation = [
                    ['dashboard', 'dashboard', 'Обзор', '01'],
                    ['contacts.index', 'contacts.*', 'Контакты', '02'],
                    ['events.index', 'events.*', 'События', '03'],
                    ['important-dates.index', 'important-dates.*', 'Важные даты', '04'],
                    ['reminders.index', 'reminders.*', 'Напоминания', '05'],
                    ['settings.telegram', 'settings.telegram*', 'Telegram', '06'],
                ])
                @foreach ($navigation as [$routeName, $activePattern, $label, $index])
                    <a class="nav-link" href="{{ route($routeName) }}" @if (request()->routeIs($activePattern)) aria-current="page" @endif>
                        <span class="nav-index">{{ $index }}</span>{{ $label }}
                    </a>
                @endforeach
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-note"><strong>{{ auth()->user()->name }}</strong>{{ auth()->user()->email }}</div>
                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-link logout-button" type="submit"><span class="nav-index">↗</span>Выйти</button>
                </form>
            </div>
        </aside>
        <main class="workspace">
            <div class="workspace-inner">
                @if (session('status'))
                    <div class="alert" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <ul class="error-list" role="alert">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>