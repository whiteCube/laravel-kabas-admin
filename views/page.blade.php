@extends('admin::layout')

@section('main')
<section class="page">
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">{{ $page->config()->name() }}</h2>
        </div>
        @if($page->route())
        {{--  <a class="topbar__link link" target="_blank" href="{{ route($page->route()) }}">Voir la page</a>  --}}
        @endif
    </div>
    <form class="page__form" method="POST" action="{{ route('kabas.admin.page.submit') }}" enctype="multipart/form-data" novalidate>
        {{ csrf_field() }}
        <input name="route" type="hidden" value="{{ $page->route() }}">
        <div class="tabs">
            <div class="tabs__header">
                <div class="tabs__dropdown">
                @foreach(Admin::locales() as $i => $lang)
                    @if($i == 0)
                    <span class="tabs__toggle">
                        <a class="tabs__link tabs__link--active" data-target="{{ $lang }}">{{ $lang }}</a>
                    </span>
                    @else
                    <a href="#" class="tabs__link" data-target="{{ $lang }}">{{ $lang }}</a>
                    @endif
                @endforeach
                </div>
            </div>
        @foreach (Admin::locales() as $i => $lang)
            <div class="tabs__item{{ $i == 0 ? ' tabs__item--active' : '' }}" id="{{ $lang }}">
                <div class="page__side">
                    <h3 class="page__sidetitle">Zones modifiables</h3>
                    <ul class="page__groups">
                        <li class="page__group">
                            <a class="page__grouplink page__grouplink--general page__grouplink--current" href="#{{$lang}}-kabas-general">General</a>
                        </li>
                        @foreach($page->fields()->tabbed() as $key => $group)
                        <li class="page__group">
                            <a class="page__grouplink" href="#{{$lang}}-{{ $key }}">{{ $group->label }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="page__editables">
                    <div class="page__editable page__editable--general" id="{{$lang}}-kabas-general">
                        <groupfield label="Page" :options="{!! $page->structure()->prefixedMeta($lang) !!}" :values="{!! $page->meta()->prefixedValues($lang) !!}"></groupfield>
                        @foreach ($page->fields()->general() as $key => $field)
                        {!! $field->render($lang) !!}
                        @endforeach
                    </div>
                    @foreach($page->fields()->tabbed() as $key => $group)
                    <div class="page__editable page__editable--hidden" id="{{$lang}}-{{ $key }}">
                        {!! $group->render($lang) !!}
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>
        <submit class="page__submit"></submit>
    </form>
</section>
@endsection
