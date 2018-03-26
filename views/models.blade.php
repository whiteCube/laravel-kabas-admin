@extends('admin::layout')

@section('main')
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">{{ $model->config()->name() }}</h2>
            <span class="topbar__counter">{{ $items->count() }}</span>
        </div>
        <btn primary icon="pages" href="{{ route('kabas.admin.model.add', ['file' => $model->structure()->route()]) }}">New entry</btn>
        @if($model->structure()->search())
        <searchbox action="#" placeholder="Search..." btntext="Search" name="search"></searchbox>
        @endif
    </div>
    @if($items->count())
    <ktable>
        <tablerow>
            @foreach($model->config()->columns() as $key => $column)
            <tableheading {{ (isset($column->main) && $column->main) ? 'main' : '' }} id="{{ $key }}">{{ $column->title }}</tableheading>
            @endforeach
            <tableheading id="actions">Actions</tableheading>
        </tablerow>
        @foreach($items as $item)
        <tablerow href="{{ route('kabas.admin.model.item', ['file' => $model->structure()->route(), 'id' => $item->id]) }}">
            @foreach($model->config()->columns() as $key => $column)
            <tablecell label="{{ $key }}">{{ $item->$key }}</tablecell>
            @endforeach
            <tablecell label="actions">
                <form action="{{ route('kabas.admin.model.delete', ['file' => $model->structure()->route(), 'id' => $item->id]) }}" method="POST">
                    {{ csrf_field() }}
                    <input type="submit" value="Delete">
                </form>
            </tablecell>
        </tablerow>
        @endforeach
    </ktable>
    @else 
    <div class="message">
        <illu class="message__illu" type="tex"></illu>
        <p class="message__text">Nothing to show</p>
    </div>
    @endif
    
    
@endsection
