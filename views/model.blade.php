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
        <input name="id" type="hidden" value="{{ $item->id }}">
        <div class="tabs">
            <div class="tabs__header">
                @if($model->hasSharedFields())
                <a href="#" class="tabs__link tabs__link--active" data-target="shared">Shared</a>
                @endif
                @foreach(Admin::locales() as $i => $lang)
                    <a href="#" class="tabs__link" data-target="{{ $lang }}">{{ $lang }}</a>
                @endforeach
            </div>
            @if($model->hasSharedFields())
            <div class="tabs__item tabs__item--active" id="shared">
                <div class="page__side">
                    <h3 class="page__sidetitle">Zones modifiables</h3>
                    <ul class="page__groups">
                        <li class="page__group">
                            <a class="page__grouplink page__grouplink--general page__grouplink--current" href="#shared-kabas-general">General</a>
                        </li>
                    @foreach($model->groups as $key => $group)
                        <li class="page__group">
                            <a class="page__grouplink" href="#shared-{{ $key }}">{{ $group->label }}</a>
                        </li>
                    @endforeach
                    </ul>
                </div>
                
                <div class="page__editables">
                    <div class="page__editable page__editable--general" id="shared-kabas-general">
                        @foreach ($model->fields as $key => $field)
                        @unless($key == 'kabas' || $key == 'translated')
                            {!! $field->render('shared') !!}
                        @endif
                        @endforeach
                    </div>
                    @foreach($model->groups as $key => $group)
                    <div class="page__editable page__editable--hidden" id="shared-{{ $key }}">
                        {!! $group->render('shared') !!}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @foreach (Admin::locales() as $i => $lang)
                <div class="tabs__item{{ !$model->hasSharedFields() && $i == 0 ? ' tabs__item--active' : '' }}" id="{{ $lang }}">

                <div class="page__side">
                    <h3 class="page__sidetitle">Zones modifiables</h3>
                    <ul class="page__groups">
                        <li class="page__group">
                            <a class="page__grouplink page__grouplink--general page__grouplink--current" href="#{{$lang}}-kabas-general">General</a>
                        </li>
                    @foreach($model->groups as $key => $group)
                        <li class="page__group">
                            <a class="page__grouplink" href="#{{$lang}}-{{ $key }}">{{ $group->label }}</a>
                        </li>
                    @endforeach
                    </ul>
                </div>

                <div class="page__editables">
                    <div class="page__editable page__editable--general" id="{{$lang}}-kabas-general">
                        {{-- @foreach ($model->fields as $key => $field)
                            {!! $field->render($lang) !!}
                        @endforeach --}}
                        @foreach ($model->fields->translated as $key => $field)
                            {!! $field->render($lang) !!}
                        @endforeach
                    </div>
                    @foreach($model->groups as $key => $group)
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
