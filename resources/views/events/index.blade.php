@extends('layouts.app')

@section('title', 'События')

@section('content')
    <header class="topline"><div><p class="eyebrow">Личная книжка · 03</p><h1>События</h1><p class="lede">Планы, встречи и договоренности.</p></div><span class="topline-date">{{ $events->total() }} записей</span></header>

    <section class="panel">
        <div class="panel-title"><h2>Новое событие</h2><span class="section-kicker">Дата и время</span></div>
        <form method="POST" action="{{ route('events.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label for="title">Название</label><input id="title" name="title" value="{{ old('title') }}" required maxlength="255"></div>
                <div class="field"><label for="contact_id">Контакт</label><select id="contact_id" name="contact_id"><option value="">Без контакта</option>@foreach ($contacts as $contact)<option value="{{ $contact->id }}" @selected(old('contact_id') == $contact->id)>{{ $contact->name }}</option>@endforeach</select></div>
                <div class="field"><label for="starts_at">Начало</label><input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" required></div>
                <div class="field"><label for="ends_at">Завершение</label><input id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}"></div>
                <div class="field"><label for="status">Состояние</label><select id="status" name="status"><option value="planned">Запланировано</option><option value="completed">Завершено</option><option value="cancelled">Отменено</option></select></div>
                <div class="field field-wide"><label for="description">Описание</label><textarea id="description" name="description" maxlength="5000">{{ old('description') }}</textarea></div>
            </div>
            <div class="form-actions"><button class="button button-accent" type="submit">Добавить событие</button></div>
        </form>
    </section>

    <section class="section">
        <div class="section-heading"><h2>Календарь событий</h2><input class="search-input" type="search" placeholder="Поиск событий" aria-label="Поиск событий" data-filter-input></div>
        <div class="record-list" data-filter-list>
            @forelse ($events as $event)
                <article class="record-row" data-filter-item data-search="{{ Str::lower($event->title.' '.$event->contact?->name.' '.$event->description) }}">
                    <div class="record-main">
                        <p class="row-title">{{ $event->title }}</p>
                        <div class="record-detail"><span>{{ $event->starts_at->format('d.m.Y · H:i') }}</span><span>{{ $event->contact?->name ?? 'Без контакта' }}</span><span class="badge badge-{{ $event->status }}">{{ $event->status }}</span></div>
                        @if ($event->description)<p class="row-meta">{{ $event->description }}</p>@endif
                        <details class="record-edit"><summary>Изменить</summary>
                            <form class="edit-form" method="POST" action="{{ route('events.update', $event) }}">
                                @csrf @method('PUT')
                                <div class="form-grid">
                                    <div class="field"><label for="title-{{ $event->id }}">Название</label><input id="title-{{ $event->id }}" name="title" value="{{ $event->title }}" required maxlength="255"></div>
                                    <div class="field"><label for="contact-{{ $event->id }}">Контакт</label><select id="contact-{{ $event->id }}" name="contact_id"><option value="">Без контакта</option>@foreach ($contacts as $contact)<option value="{{ $contact->id }}" @selected($event->contact_id === $contact->id)>{{ $contact->name }}</option>@endforeach</select></div>
                                    <div class="field"><label for="starts-{{ $event->id }}">Начало</label><input id="starts-{{ $event->id }}" name="starts_at" type="datetime-local" value="{{ $event->starts_at->format('Y-m-d\TH:i') }}" required></div>
                                    <div class="field"><label for="ends-{{ $event->id }}">Завершение</label><input id="ends-{{ $event->id }}" name="ends_at" type="datetime-local" value="{{ $event->ends_at?->format('Y-m-d\TH:i') }}"></div>
                                    <div class="field"><label for="status-{{ $event->id }}">Состояние</label><select id="status-{{ $event->id }}" name="status"><option value="planned" @selected($event->status === 'planned')>Запланировано</option><option value="completed" @selected($event->status === 'completed')>Завершено</option><option value="cancelled" @selected($event->status === 'cancelled')>Отменено</option></select></div>
                                    <div class="field field-wide"><label for="description-{{ $event->id }}">Описание</label><textarea id="description-{{ $event->id }}" name="description" maxlength="5000">{{ $event->description }}</textarea></div>
                                </div>
                                <div class="form-actions"><button class="button button-small" type="submit">Сохранить</button></div>
                            </form>
                        </details>
                    </div>
                    <form method="POST" action="{{ route('events.destroy', $event) }}" data-confirm="Удалить событие и напоминания?">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit" aria-label="Удалить событие {{ $event->title }}">Удалить</button></form>
                </article>
            @empty
                <div class="empty-state"><strong>Событий пока нет</strong>Добавьте встречу или план.</div>
            @endforelse
        </div>
        <div class="pagination">{{ $events->links() }}</div>
    </section>
@endsection