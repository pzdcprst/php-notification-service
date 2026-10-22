@extends('layouts.guest')

@section('title', 'Вход')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <p class="eyebrow">С возвращением</p>
        <h2>Войти в книжку</h2>
        <p class="auth-subtitle">Продолжите с того места, где остановились.</p>
        @if ($errors->any())
            <ul class="error-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        @endif
        <div class="field"><label for="email">Электронная почта</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
        <div class="field"><label for="password">Пароль</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
        <label class="check-row"><input type="checkbox" name="remember" value="1"> Не выходить из аккаунта</label>
        <button class="button button-accent" type="submit">Войти</button>
        <p class="auth-switch">Еще нет аккаунта? <a href="{{ route('register') }}">Создать</a></p>
    </form>
@endsection