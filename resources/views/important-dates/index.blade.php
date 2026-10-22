@extends('layouts.app')

@section('title', 'Важные даты')

@section('content')
    <header class="topline"><div><p class="eyebrow">Личная книжка · 04</p><h1>Важные даты</h1><p class="lede">Дни рождения и другие даты, которые повторяются.</p></div><span class="topline-date">{{ $importantDates->total() }} записей</span></header>

    <section class="panel">
        <div class="panel-title"><h2>Новая дата</h2><span class="section-kicker">Для контакта</span></div>
        <form method="POST" action="{{ route('important-dates.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field"><label for="contact_id">Контакт</label><select id="contact_id" name="contact_id" required><option value="">Выберите контакт</option>@foreach ($contacts as $contact)<option value="{{ $contact->id }}" @selected(old('contact_id') == $contact->id)>{{ $contact->name }}</option>@endforeach</select></div>
                <div class="field"><label for="title">Название</label><input id="title" name="title" value="{{ old('title') }}" placeholder="День рождения" required maxlength="255"></div>
                <div class="field"><label for="date">Дата</label><input id="date" name="date" type="date" value="{{ old('date') }}" required></div>
                <label class="check-row"><input type="hidden" name="repeat_yearly" value="0"><input type="checkbox" name="repeat_yearly" value="1" checked> Повторять каждый год</label>
            </div>
            <div class="form-actions"><button class="button button-accent" type="submit">Добавить дату</button></div>
        </form>
    </section>

    <section class="section">
        <div class="section-heading"><h2>Список дат</h2><input class="search-input" type="search" placeholder="Поиск дат" aria-label="Поиск дат" data-filter-input></div>
        <div class="record-list" data-filter-list>
            @forelse ($importantDates as $importantDate)
                <article class="record-row" data-filter-item data-search="{{ Str::lower($importantDate->title.' '.$importantDate->contact->name) }}">
                    <div class="record-main">
                        <p class="row-title">{{ $importantDate->title }}</p>
                        <div class="record-detail"><span>{{ $importantDate->date->format('d.m.Y') }}</span><span>{{ $importantDate->contact->name }}</span>@if ($importantDate->repeat_yearly)<span class="badge badge-planned">ежегодно</span>@endif</div>
                        <details class="record-edit"><summary>Изменить</summary>
                            <form class="edit-form" method="POST" action="{{ route('important-dates.update', $importantDate) }}">
                                @csrf @method('PUT')
                                <div class="form-grid">
                                    <div class="field"><label for="date-contact-{{ $importantDate->id }}">Контакт</label><select id="date-contact-{{ $importantDate->id }}" name="contact_id" required>@foreach ($contacts as $contact)<option value="{{ $contact->id }}" @selected($importantDate->contact_id === $contact->id)>{{ $contact->name }}</option>@endforeach</select></div>
                                    <div class="field"><label for="date-title-{{ $importantDate->id }}">Название</label><input id="date-title-{{ $importantDate->id }}" name="title" value="{{ $importantDate->title }}" required maxlength="255"></div>
                                    <div class="field"><label for="date-value-{{ $importantDate->id }}">Дата</label><input id="date-value-{{ $importantDate->id }}" name="date" type="date" value="{{ $importantDate->date->format('Y-m-d') }}" required></div>
                                    <label class="check-row"><input type="hidden" name="repeat_yearly" value="0"><input type="checkbox" name="repeat_yearly" value="1" @checked($importantDate->repeat_yearly)> Повторять каждый год</label>
                                </div>
                                <div class="form-actions"><button class="button button-small" type="submit">Сохранить</button></div>
                            </form>
                        </details>
                    </div>
                    <form method="POST" action="{{ route('important-dates.destroy', $importantDate) }}" data-confirm="Удалить важную дату?">@csrf @method('DELETE')<button class="button button-danger button-small" type="submit" aria-label="Удалить {{ $importantDate->title }}">Удалить</button></form>
                </article>
            @empty
                <div class="empty-state"><strong>Важных дат пока нет</strong>Добавьте дату для одного из контактов.</div>
            @endforelse
        </div>
        <div class="pagination">{{ $importantDates->links() }}</div>
    </section>
@endsection