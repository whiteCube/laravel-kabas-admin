@extends('admin::layout')

@section('main')
<section class="page">

    <div class="topbar">
        <h2 class="topbar__title">Deleting {{ $model->config()->name() }}</h2>
        <a href="{{ route('kabas.admin.model', ['route' => $model->structure()->route()]) }}" class="topbar__link link">Back to list</a>
    </div>

    <form class="page__form" method="POST" action="{{ route('kabas.admin.model.destroy', ['file' => $model->structure()->route(), 'id' => $item->id]) }}">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="delete" />
        <input name="structure" type="hidden" value="{{ $model->structure()->route() }}">
        <input name="id" type="hidden" value="{{ $item->id }}">
        
        <p>Are you sure you want to delete this model?</p>

        <button class="submit" type="submit">Delete</button>

    </form>
</section>
@endsection
