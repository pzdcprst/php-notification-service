@extends('layouts.app')

@section('title', 'Telegram')

@section('content')
    <header class="topline"><div><p class="eyebrow">Настройки · 06</p><h1>Telegram</h1><p class="lede">Подключите личный чат, чтобы получать напоминания вне приложения.</p></div></header>

    @if ($errors->has('telegram'))
        <div class="alert alert-error" role="alert">{{ $errors->first('telegram') }}</div>
    @endif

    <section class="telegram-box">
        <div>
            <div class="telegram-state"><span class="state-dot @if ($user->hasTelegram()) is-on @endif"></span><strong>{{ $user->hasTelegram() ? 'Telegram подключен' : 'Telegram не подключен' }}</strong></div>
            <p class="row-meta">{{ $user->hasTelegram() ? 'Напоминания будут приходить в ваш личный чат.' : 'Создайте одноразовую ссылку и откройте бота в Telegram.' }}</p>
        </div>
        @if ($user->hasTelegram())
            <form method="POST" action="{{ route('settings.telegram.disconnect') }}" data-confirm="Отключить Telegram?">
                @csrf @method('DELETE')
                <button class="button button-danger" type="submit">Отключить</button>
            </form>
        @else
            <form method="POST" action="{{ route('settings.telegram.link') }}">
                @csrf
                <button class="button button-accent" type="submit">Создать ссылку подключения</button>
            </form>
        @endif
    </section>

    @if (session('telegramLink'))
        <section class="panel section">
            <p class="eyebrow">Ссылка действует 15 минут</p>
            <h2>Откройте бота</h2>
            <p class="row-meta">Одноразовый код активируется после нажатия Start в Telegram.</p>
            <a class="button button-accent" href="{{ session('telegramLink') }}" target="_blank" rel="noopener noreferrer">Открыть Telegram</a>
            <div class="telegram-url">{{ session('telegramLink') }}</div>
        </section>
    @endif
@endsection