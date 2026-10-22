@extends('layouts.guest')

@section('title', 'Регистрация')

@section('content')
    <form class="auth-form" method="POST" action="{{ route('register.store') }}">
        @csrf
        <p class="eyebrow">Начать с чистого листа</p>
        <h2>Создать книжку</h2>
        <p class="auth-subtitle">Ваши записи будут доступны только вам.</p>
        @if ($errors->any())
            <ul class="error-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        @endif
        <div class="field"><label for="name">Имя</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="255" autofocus></div>
        <div class="field"><label for="email">Электронная почта</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required></div>
        <div class="field"><label for="password">Пароль</label><input id="password" name="password" type="password" autocomplete="new-password" required minlength="10"></div>
        <div class="field"><label for="password_confirmation">Повторите пароль</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
        <button class="button button-accent" type="submit">Создать аккаунт</button>
        <p class="auth-switch">Уже есть аккаунт? <a href="{{ route('login') }}">Войти</a></p>
    </form>
@endsection