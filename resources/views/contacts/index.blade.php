@extends('layouts.app')

@section('title', 'Контакты')

@section('content')
    <header class="topline">
        <div><p class="eyebrow">Личная книжка · 02</p><h1>Контакты</h1><p class="lede">Люди, которых хочется помнить.</p></div>
        <span class="topline-date">{{ $contacts->total() }} записей</span>
    </header>

    <section class="panel">
        <div class="panel-title"><h2>Новый контакт</h2><span class="section-kicker">{{ auth()->user()->name }}</span></div>
        <form method="POST" action="{{ route('contacts.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label for="name">Имя</label><input id="name" name="name" value="{{ old('name') }}" required maxlength="255"></div>
                <div class="field"><label for="phone">Телефон</label><input id="phone" name="phone" type="tel" value="{{ old('phone') }}" maxlength="50"></div>
                <div class="field"><label for="email">Электронная почта</label><input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255"></div>
                <div class="field field-wide"><label for="notes">Заметки</label><textarea id="notes" name="notes" maxlength="5000">{{ old('notes') }}</textarea></div>
            </div>
            <div class="form-actions"><button class="button button-accent" type="submit">Добавить контакт</button></div>
        </form>
    </section>

    <section class="section">
        <div class="section-heading">
            <h2>Записная книжка</h2>
            <div class="toolbar"><input class="search-input" type="search" placeholder="Поиск контактов" aria-label="Поиск контактов" data-filter-input><span class="section-kicker">{{ $contacts->total() }} всего</span></div>
        </div>
        <div class="record-list" data-filter-list>
            @forelse ($contacts as $contact)
                <article class="record-row" data-filter-item data-search="{{ Str::lower($contact->name.' '.$contact->email.' '.$contact->phone.' '.$contact->notes) }}">
                    <div class="record-main">
                        <p class="row-title">{{ $contact->name }}</p>
                        <div class="record-detail">
                            @if ($contact->phone)<span>{{ $contact->phone }}</span>@endif
                            @if ($contact->email)<span>{{ $contact->email }}</span>@endif
                            <span>{{ $contact->events_count }} событий</span><span>{{ $contact->important_dates_count }} дат</span>
                        </div>
                        @if ($contact->notes)<p class="row-meta">{{ $contact->notes }}</p>@endif
                        <details class="record-edit"><summary>Изменить</summary>
                            <form class="edit-form" method="POST" action="{{ route('contacts.update', $contact) }}">
                                @csrf @method('PUT')
                                <div class="form-grid">
                                    <div class="field"><label for="name-{{ $contact->id }}">Имя</label><input id="name-{{ $contact->id }}" name="name" value="{{ $contact->name }}" required maxlength="255"></div>
                                    <div class="field"><label for="phone-{{ $contact->id }}">Телефон</label><input id="phone-{{ $contact->id }}" name="phone" value="{{ $contact->phone }}" maxlength="50"></div>
                                    <div class="field"><label for="email-{{ $contact->id }}">Электронная почта</label><input id="email-{{ $contact->id }}" name="email" type="email" value="{{ $contact->email }}" maxlength="255"></div>
                                    <div class="field field-wide"><label for="notes-{{ $contact->id }}">Заметки</label><textarea id="notes-{{ $contact->id }}" name="notes" maxlength="5000">{{ $contact->notes }}</textarea></div>
                                </div>
                                <div class="form-actions"><button class="button button-small" type="submit">Сохранить</button></div>
                            </form>
                        </details>
                    </div>
                    <form method="POST" action="{{ route('contacts.destroy', $contact) }}" data-confirm="Удалить контакт и связанные записи?">
                        @csrf @method('DELETE')
                        <button class="button button-danger button-small" type="submit" aria-label="Удалить {{ $contact->name }}">Удалить</button>
                    </form>
                </article>
            @empty
                <div class="empty-state"><strong>Здесь пока пусто</strong>Добавьте первый контакт.</div>
            @endforelse
        </div>
        <div class="pagination">{{ $contacts->links() }}</div>
    </section>
@endsection