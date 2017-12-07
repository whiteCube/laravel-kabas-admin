@extends('admin::layout')

@section('main')
<section class="page">

    <div class="topbar">
        <h2 class="topbar__title">{{ $model->name }}</h2>
        <a href="{{ route('kabas.admin.model', ['file' => $model->file]) }}" class="topbar__link link">Back to list</a>
    </div>

    <form class="page__form" method="POST" action="{{ route('kabas.admin.model.submit') }}">
        {{ csrf_field() }}
        <input name="structure" type="hidden" value="{{ $model->structure }}">

        <div class="page__shared">
        @foreach ($model->sharedFields() as $key => $field)
            <genericfield name="{{ $key }}" :structure="{{ json_encode($field) }}" value="{{ $item->$key }}" ></genericfield>
        @endforeach
        </div>

        <div class="tabs">
            <div class="tabs__header">
                @foreach(Admin::locales() as $lang)
                    <a href="#" class="tabs__link" data-target="{{ $lang }}">{{ $lang }}</a>
                @endforeach
            </div>
            @foreach (Admin::locales() as $lang)
                <div class="tabs__item" id="{{ $lang }}">

                @foreach ($model->fields->translated as $key => $field)
                    <genericfield name="{{ $lang . '|' .$key }}" :structure="{{ json_encode($field) }}" value="{{ $item->translate($lang)->$key }}" ></genericfield>
                @endforeach
                </div>
            @endforeach
        </div>

        <submit></submit>

    </form>
</section>
@endsection
