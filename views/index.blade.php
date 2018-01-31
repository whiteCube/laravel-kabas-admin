@extends('admin::layout')

@section('main')
    <div class="topbar">
        <div class="topbar__text">
            <h2 class="topbar__title">Pages</h2>
            <span class="topbar__counter">{{ count($pages) }}</span>
        </div>
    </div>
    
    <section>
    @if(Admin::tableview())
        <ktable>
            <tablerow>
                <tableheading main id="page">Page</tableheading>
                <tableheading id="lastmod">Last edit</tableheading>
            </tablerow>
            @foreach($pages as $page)
            <tablerow href="{{ route('kabas.admin.page', ['route' => $page->route()]) }}">
                <tablecell label="page">{{ $page->config()->name() }}</tablecell>
                <tablecell label="lastmod">{{ $page->lastModified()->format('d-m-Y H:i') }}</tablecell>
            </tablerow>
            @endforeach
        </ktable>
    @else
    @foreach($pages as $page)
        <card edit="{{ route('kabas.admin.page', ['route' => $page->route()]) }}" view="{{ $page->route() }}" icon="{{ $page->config()->icon() }}">
            <template slot="title">{{ $page->config()->name() }}</template>
            <template slot="edit">Edit this page</template>
            <template slot="editedlabel">Last modified</template>
            <template slot="edited">{{ $page->lastModified()->format('d-m-Y H:i') }}</template>
            <template slot="description">{{ str_limit($page->meta()->get('description')->value(Lang::locale()), 155) }}</template>
            <template slot="view">View this page</template>
        </card>
    @endforeach 
    </section>
    @endif
    
@endsection
