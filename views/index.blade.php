@extends('admin::layout')

@section('main')
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">Pages</h2>
            <span class="topbar__counter">{{ count($pages) }}</span>
        </div>
    </div>
    
    <section>
    @foreach($pages as $page)
        <card edit="{{ route('kabas.admin.page', ['file' => $page->structure()->file()]) }}" view="{{ $page->route() }}" icon="{{ $page->config()->icon() }}">
            <template slot="title">{{ $page->config()->name() }}</template>
            <template slot="edit">Edit this page</template>
            <template slot="editedlabel">Last modified</template>
            <template slot="edited">{{ $page->lastModified()->format('d-m-Y H:i') }}</template>
            <template slot="description">{{ str_limit($page->meta()->get('description')->value(Lang::locale()), 155) }}</template>
            <template slot="view">View this page</template>
        </card>
    @endforeach 
    </section>
    
@endsection
