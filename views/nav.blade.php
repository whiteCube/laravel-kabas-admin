<nav class="kabasnav">
    <h2 class="kabasnav__title">Main navigation</h2>
    <a class="kabasnav__link kabasnav__link--primary {{ url()->current() == route('kabas.admin') ? 'kabasnav__link--active' : '' }}" href="{{ route('kabas.admin') }}">Administration</a>
    @foreach (Admin::customs() as $custom)
        <a class="kabasnav__link {{ url()->current() == route('kabas.admin.custom', ['file' => $custom->route()]) ? 'kabasnav__link--active' : '' }}" href="{{ route('kabas.admin.custom', ['file' => $custom->route()]) }}">
            {{ $custom->config()->name() }}
        </a>
    @endforeach
    <em class="kabasnav__subtitle">Pages</em>
    @foreach (Admin::pages()->sorted() as $page)
        <a class="kabasnav__link {{ url()->current() == route('kabas.admin.page', ['file' => $page->route()]) ? 'kabasnav__link--active' : '' }}" href="{{ route('kabas.admin.page', ['file' => $page->route()]) }}">
            {{ $page->config()->name() }}
        </a>
    @endforeach
    <em class="kabasnav__subtitle">Modèles</em>
    @foreach (Admin::models()->sorted() as $model)
        <a class="kabasnav__link {{ url()->current() == route('kabas.admin.model', ['file' => $model->structure()->route()]) ? 'kabasnav__link--active' : '' }}" href="{{ route('kabas.admin.model', ['file' => $model->structure()->route()]) }}">
            {{ $model->config()->name() }}
        </a>
    @endforeach
</nav>