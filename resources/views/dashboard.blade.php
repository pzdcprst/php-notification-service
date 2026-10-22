@extends('layouts.app')

@section('title', 'Обзор')

@section('content')
    <header class="topline">
        <div>
            <p class="eyebrow">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1>Здравствуйте, {{ Str::before(auth()->user()->name, ' ') }}</h1>
            <p class="lede">Ваши люди, планы и даты в одном месте.</p>
        </div>
        <span class="topline-date">Личная книжка</span>
    </header>

    <section class="stats" aria-label="Сводка">
        <div class="stat"><span class="stat-value">{{ $contactsCount }}</span><span class="stat-label">Контактов</span></div>
        <div class="stat"><span class="stat-value">{{ $eventsCount }}</span><span class="stat-label">Предстоящих событий</span></div>
        <div class="stat"><span class="stat-value">{{ $importantDatesCount }}</span><span class="stat-label">Важных дат</span></div>
    </section>

    <section class="quick-links" aria-label="Переходы">
        <a class="quick-link" href="{{ route('contacts.index') }}">Контакты <span>↗</span></a>
        <a class="quick-link" href="{{ route('events.index') }}">События <span>↗</span></a>
        <a class="quick-link" href="{{ route('important-dates.index') }}">Важные даты <span>↗</span></a>
        <a class="quick-link" href="{{ route('reminders.index') }}">Напоминания <span>↗</span></a>
    </section>

    <div class="dashboard-grid section">
        <section>
            <div class="section-heading">
                <h2>Ближайшие события</h2>
                <a class="section-kicker" href="{{ route('events.index') }}">Все события ↗</a>
            </div>
            @forelse ($upcomingEvents as $event)
                <article class="event-row">
                    <div class="date-block"><strong>{{ $event->starts_at->format('d') }}</strong>{{ $event->starts_at->translatedFormat('M · H:i') }}</div>
                    <div><p class="row-title">{{ $event->title }}</p><p class="row-meta">{{ $event->contact?->name ?? 'Без контакта' }}</p></div>
                    <span class="badge badge-{{ $event->status }}">{{ $event->status }}</span>
                </article>
            @empty
                <div class="empty-state"><strong>Пока нет событий</strong>Добавьте планы, которые важно держать в поле зрения.</div>
            @endforelse
        </section>

        <section>
            <div class="section-heading"><h2>Напоминания</h2><a class="section-kicker" href="{{ route('reminders.index') }}">Все ↗</a></div>
            @forelse ($pendingReminders as $reminder)
                <article class="record-row">
                    <div class="record-main"><p class="row-title">{{ $reminder->event->title }}</p><p class="row-meta">{{ $reminder->remind_at->format('d.m.Y · H:i') }}</p></div>
                    <span class="badge badge-pending">{{ $reminder->channel }}</span>
                </article>
            @empty
                <div class="empty-state"><strong>Нет ожидающих</strong>Все напоминания на месте.</div>
            @endforelse
        </section>
    </div>
@endsection