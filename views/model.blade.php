@extends('admin::layout')

@section('main')
<section class="page">

    <div class="topbar">
        <h2 class="topbar__title">{{ $model->config()->name() }}</h2>
        <a href="{{ route('kabas.admin.model', ['route' => $model->structure()->route()]) }}" class="topbar__link link">Back to list</a>
    </div>

    @if($errors->any())
    <div class="errorbox">
        Il y a des erreurs. Veuillez vérifier les données et soumettre le formulaire à nouveau.
    </div>
    @endif

    <form class="page__form" enctype="multipart/form-data" method="POST" action="{{ route('kabas.admin.model.submit') }}">
        {{ csrf_field() }}
        <input name="structure" type="hidden" value="{{ $model->structure()->route() }}">
        <input name="id" type="hidden" value="{{ $item->id }}">
        <div class="tabs">
            <div class="tabs__header">
                @if($model->fields()->hasTranslated())
                <div class="tabs__dropdown">
                @if($model->fields()->hasShared())
                    <span class="tabs__toggle">
                        <a href="#" class="tabs__link tabs__link--active" data-target="shared">Shared</a>
                    </span>
                @endif
                @foreach(Admin::locales() as $i =>$locale)
                    @if(!$model->fields()->hasShared() && $i == 0)
                    <span class="tabs__toggle">
                        <a class="tabs__link tabs__link--active" data-target="{{ $locale }}">{{ $locale }}</a>
                    </span>
                    @else
                    <a href="#" class="tabs__link" data-target="{{ $locale }}">{{ $locale }}</a>
                    @endif
                @endforeach
                </div>
                @endif
            </div>
            @if($model->fields()->hasShared())
            <div class="tabs__item tabs__item--active" id="shared">
                <div class="page__side">
                    <h3 class="page__sidetitle">Zones modifiables</h3>
                    <ul class="page__groups">
                        <li class="page__group">
                            <a class="page__grouplink page__grouplink--general page__grouplink--current" href="#shared-kabas-general">General</a>
                        </li>
                    @foreach($model->fields()->shared() as $key => $field)
                    @if($field->isTabbedGroup())
                        <li class="page__group">
                            <a class="page__grouplink" href="#shared-{{ $key }}">{{ $field->label }}</a>
                        </li>
                    @endif
                    @endforeach
                    </ul>
                </div>
                
                <div class="page__editables">
                    <div class="page__editable page__editable--general" id="shared-kabas-general">
                        @foreach ($model->fields()->shared() as $key => $field)
                        @if($errors->has($key))
                        @php
                        $field->addError($errors->first($key));
                        @endphp
                        @endif
                        @unless($field->isTabbedGroup())
                            {!! $field->render('shared') !!}
                        @endif
                        @endforeach
                    </div>
                    @foreach($model->fields()->tabbed() as $key => $group)
                    <div class="page__editable page__editable--hidden" id="shared-{{ $key }}">
                        {!! $group->render('shared') !!}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @foreach (Admin::locales() as $i => $locale)
                <div class="tabs__item{{ !$model->fields()->hasShared() && $i == 0 ? ' tabs__item--active' : '' }}" id="{{ $locale }}">

                <div class="page__side">
                    <h3 class="page__sidetitle">Zones modifiables</h3>
                    <ul class="page__groups">
                        <li class="page__group">
                            <a class="page__grouplink page__grouplink--general page__grouplink--current" href="#{{$locale}}-kabas-general">General</a>
                        </li>
                    @foreach($model->fields()->translated() as $key => $field)
                    @if($field->isTabbedGroup())
                        <li class="page__group">
                            <a class="page__grouplink" href="#{{$locale}}-{{ $key }}">{{ $field->label }}</a>
                        </li>
                    @endif
                    @endforeach
                    </ul>
                </div>
                @if($model->fields()->hasTranslated())
                <div class="page__editables">
                    <div class="page__editable page__editable--general" id="{{$locale}}-kabas-general">
                        @foreach ($model->fields()->translated() as $key => $field)
                            @if($errors->has($locale . '|' . $key))
                            @php
                            $field->addError($errors->first($locale . '|' . $key));
                            @endphp
                            @endif
                            {!! $field->render($locale) !!}
                        @endforeach
                    </div>
                    @foreach($model->fields()->tabbed() as $key => $group)
                    @if($errors->has($locale . '|' . $key))
                    @php
                    $group->addError($errors->first($locale . '|' . $key));
                    @endphp
                    @endif
                    <div class="page__editable page__editable--hidden" id="{{$locale}}-{{ $key }}">
                        {!! $group->render($locale) !!}
                    </div>
                    @endforeach
                </div>
                @endif
                </div>
            @endforeach
        </div>

        <submit class="page__submit"></submit>

    </form>
</section>
@endsection
