@extends('layouts.app')

@section('title', 'Напоминания')

@section('content')
    <header class="topline"><div><p class="eyebrow">Личная книжка · 05</p><h1>Напоминания</h1><p class="lede">Отправка выполняется сервером, даже когда браузер закрыт.</p></div><span class="topline-date">{{ $reminders->total() }} записей</span></header>

    <section class="panel">
        <div class="panel-title"><h2>Новое напоминание</h2><span class="section-kicker">Очередь · Telegram</span></div>
        <form method="POST" action="{{ route('reminders.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field field-wide"><label for="event_id">Событие</label><select id="event_id" name="event_id" required><option value="">Выберите событие</option>@foreach ($events as $event)<option value="{{ $event->id }}" @selected(old('event_id') == $event->id)>{{ $event->title }} · {{ $event->starts_at->format('d.m.Y H:i') }}</option>@endforeach</select></div>
                <div class="field"><label for="remind_at">Когда напомнить</label><input id="remind_at" name="remind_at" type="datetime-local" value="{{ old('remind_at', now()->addHour()->format('Y-m-d\TH:i')) }}" required></div>
                <div class="field"><label for="channel">Канал</label><select id="channel" name="channel" required><option value="telegram" @selected(old('channel', 'telegram') === 'telegram')>Telegram</option><option value="database" @selected(old('channel') === 'database')>В приложении</option></select></div>
            </div>
            <div class="form-actions"><button class="button button-accent" type="submit">Создать напоминание</button>@unless (auth()->user()->hasTelegram())<a class="button button-outline" href="{{ route('settings.telegram') }}">Подключить Telegram</a>@endunless</div>
        </form>
    </section>

    <section class="section">
        <div class="section-heading"><h2>Все напоминания</h2><input class="search-input" type="search" placeholder="Поиск напоминаний" aria-label="Поиск напоминаний" data-filter-input></div>
        <div class="record-list" data-filter-list>
            @forelse ($reminders as $reminder)
                <article class="record-row" data-filter-item data-search="{{ Str::lower($reminder->event->title.' '.$reminder->channel.' '.$reminder->status) }}">
                    <div class="record-main">
                        <p class="row-title">{{ $reminder->event->title }}</p>
                        <div class="record-detail"><span>{{ $reminder->remind_at->format('d.m.Y · H:i') }}</span><span>{{ $reminder->channel === 'telegram' ? 'Telegram' : 'В приложении' }}</span><span class="badge badge-{{ $reminder->status }}">{{ $reminder->status }}</span></div>
                        <details class="record-edit"><summary>Изменить</summary>
                            <form class="edit-form" method="POST" action="{{ route('reminders.update', $reminder) }}">
                                @csrf @method('PUT')
                                <div class="form-grid">
                                    <div class="field field-wide"><label for="reminder-event-{{ $reminder->id }}">Событие</label><select id="reminder-event-{{ $reminder->id }}" name="event_id" required>@foreach ($events as $event)<option value="{{ $event->id }}" @selected($reminder->event_id === $event->id)>{{ $event->title }} · {{ $event->starts_at->format('d.m.Y H:i') }}</option>@endforeach</select></div>
                                    <div class="field"><label for="reminder-time-{{ $reminder->id }}">Когда напомнить</label><input id="reminder-time-{{ $reminder->id }}" name="remind_at" type="datetime-local" value="{{ $reminder->remind_at->format('Y-m-d\TH:i') }}" required></div>
                                    <div class="field"><label for="reminder-channel-{{ $reminder->id }}">Канал</label><select id="reminder-channel-{{ $reminder->id }}" name="channel"><option value="telegram" @selected($reminder->channel === 'telegram')>Telegram</option><option value="database" @selected($reminder->channel === 'database')>В приложении</option></select></div>
                                </div>
                                <div class="form-actions"><button class="button button-small" type="submit">Сохранить</button></div>
                            </form>
                        </details>
                    </div>
                    <form method="POST" action="{{ route('reminders.destroy', $reminder) }}" data-confirm="Удалить напоминание?">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit" aria-label="Удалить напоминание к {{ $reminder->event->title }}">Удалить</button></form>
                </article>
            @empty
                <div class="empty-state"><strong>Напоминаний пока нет</strong>Создайте напоминание для события.</div>
            @endforelse
        </div>
        <div class="pagination">{{ $reminders->links() }}</div>
    </section>
@endsection