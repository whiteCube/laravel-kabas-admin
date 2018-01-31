@extends('admin::layout')

@section('main')
<section class="page">
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">{{ $custom->config()->name() }}</h2>
        </div>
    </div>
    {!! $custom->output() !!}
</section>
@endsection
