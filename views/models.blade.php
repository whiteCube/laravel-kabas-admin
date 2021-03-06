@extends('admin::layout')

@section('main')
<form method="GET">
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">{{ $model->config()->name() }}</h2>
            <span class="topbar__counter">{{ $items->total() }}</span>
        </div>
        @if(null === ($model->config()->createButton()) || $model->config()->createButton())
        <btn primary icon="pages" href="{{ route('kabas.admin.model.add', ['file' => $model->structure()->route()]) }}">New entry</btn>
        @endif
        @if($model->structure()->search())
        <searchfield placeholder="Search..." btntext="Search" value="{{ request()->search }}" name="search"></searchfield>
        @endif
    </div>
    @if($model->config()->filters())
        <div class="filters">
            <div class="filters__items">
                @foreach($model->config()->filters() as $key => $filter)
                    <div class="field checkbox filters__item">
                        <input class="field__element" type="checkbox" name="{{$key}}" id="{{ $key}}" {{ request()->get($key) ? 'checked' : '' }}>
                        <label class="field__label" for="{{ $key}}">{{$filter->label}}</label>
                    </div>
                @endforeach
            </div>
            <button class="btn" type="submit">Appliquer les filtres</button>
        </div>
    @endif
</form>
    @if($items->count())
    <ktable>
        <tablerow>
            @foreach($model->config()->columns() as $key => $column)
            <tableheading {{ (isset($column->main) && $column->main) ? 'main' : '' }} id="{{ $key }}">{{ $column->title }}</tableheading>
            @endforeach
            @if(null === ($model->config()->deleteButton()) || $model->config()->deleteButton())
            <tableheading right id="actions">Actions</tableheading>
            @endif
        </tablerow>
        @foreach($items as $item)
        <tablerow href="{{ route('kabas.admin.model.item', ['file' => $model->structure()->route(), 'id' => $item->id]) }}">
            @foreach($model->config()->columns() as $key => $column)
            <tablecell label="{{ $key }}">{{ $item->$key }}</tablecell>
            @endforeach
            @if(null === ($model->config()->deleteButton()) || $model->config()->deleteButton())
            <tablecell right label="actions">
                <form action="{{ route('kabas.admin.model.delete', ['file' => $model->structure()->route(), 'id' => $item->id]) }}" method="POST">
                    {{ csrf_field() }}
                    <input type="submit" class="btn btn--tiny btn--primary" value="Delete">
                </form>
            </tablecell>
            @endif
        </tablerow>
        @endforeach
    </ktable>
        {{ $items->links() }}
    @else
    <div class="message">
        <illu class="message__illu" type="tex"></illu>
        <p class="message__text">Nothing to show</p>
    </div>
    @endif


@endsection
