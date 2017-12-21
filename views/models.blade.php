@extends('admin::layout')

@section('main')
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">{{ $model->name }}</h2>
            <span class="topbar__counter">{{ $model->items()->count() }}</span>
        </div>
        <btn primary icon="pages" href="#">New entry</btn>
    </div>

    <ktable>
        <tablerow>
            @foreach($model->config->columns as $key => $column)
            <tableheading {{ (isset($column->main) && $column->main) ? 'main' : '' }} id="{{ $key }}">{{ $column->title }}</tableheading>
            @endforeach
            {{-- <tableheading id="lastmod">Last edit</tableheading> --}}
        </tablerow>
        @foreach($model->items() as $item)
        <tablerow href="{{ route('kabas.admin.model.item', ['file' => $model->file, 'id' => $item->id]) }}">
            @foreach($model->config->columns as $key => $column)
            <tablecell label="{{ $key }}">{{ $item->$key }}</tablecell>
            @endforeach
        </tablerow>
        @endforeach
    </ktable>
    
    
@endsection
