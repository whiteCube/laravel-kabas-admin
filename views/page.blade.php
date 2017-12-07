@extends('admin::layout')

@section('main')
<section class="page">
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">{{ $page->name }}</h2>
        </div>
        <a class="topbar__link link" target="_blank" href="{{ route($page->url) }}">{{ route($page->url) }}</a>
    </div>
    <div class="page__info">
        
    </div>
    <form class="page__form" method="POST" action="{{ route('kabas.admin.page.submit') }}">
        {{ csrf_field() }}
        <input name="structure" type="hidden" value="{{ $page->structure }}">
        <div class="tabs">
            <div class="tabs__header">
                @foreach(Admin::locales() as $i =>$lang)
                    <a href="#" class="tabs__link{{ $i == 0 ? ' tabs__link--active' : '' }}" data-target="{{ $lang }}">{{ $lang }}</a>
                @endforeach
            </div>
        @foreach (Admin::locales() as $i => $lang)
            <div class="tabs__item{{ $i == 0 ? ' tabs__item--active' : '' }}" id="{{ $lang }}">

            <groupfield label="Metadata" :options="{
                title: {
                    type: 'text',
                    name: '{{ $lang }}|title',
                    label: 'Title',
                    rules: {
                        limit: 100
                    }
                }
                }" :values="{ title: '{{ $page->value('title', $lang) }}' }"></groupfield>

            @foreach ($page->fields as $key => $field)

                {!! $field->render($lang) !!}
                
            @endforeach
            </div>
        @endforeach
        </div>

        <submit></submit>

    </form>
</section>
@endsection
