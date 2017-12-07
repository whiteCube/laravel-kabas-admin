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
        <card edit="{{ route('kabas.admin.page', ['file' => $page->url]) }}" view="{{ route($page->url) }}" icon="{{ $page->config->icon ?? 'home' }}">
            <template slot="title">{{ $page->name }}</template>
            <template slot="edit">Edit this page</template>
            <template slot="editedlabel">Last modified</template>
            <template slot="edited">{{ $page->lastModified()->format('d-m-Y H:i') }}</template>
            <template slot="description">{{ str_limit('test') }}</template>
            <template slot="view">View this page</template>
        </card>
    @endforeach 
    </section>
    
@endsection
