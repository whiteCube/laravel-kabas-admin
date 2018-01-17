<nav class="kabasnav">
    <h2 class="kabasnav__title">Main navigation</h2>
    <a class="kabasnav__link kabasnav__link--primary {{ url()->current() == route('kabas.admin') ? 'kabasnav__link--active' : '' }}" href="{{ route('kabas.admin') }}">Administration</a>
    <em class="kabasnav__subtitle">Pages</em>
    @foreach (Admin::pages() as $page)
        <a class="kabasnav__link {{ url()->current() == route('kabas.admin.page', ['file' => $page->url]) ? 'kabasnav__link--active' : '' }}" href="{{ route('kabas.admin.page', ['file' => $page->url]) }}">
            {{ $page->name }}
        </a>
    @endforeach
    <em class="kabasnav__subtitle">Modèles</em>
    @foreach (Admin::models() as $model)
        <a class="kabasnav__link {{ url()->current() == route('kabas.admin.model', ['file' => $model->file]) ? 'kabasnav__link--active' : '' }}" href="{{ route('kabas.admin.model', ['file' => $model->file]) }}">
            {{ $model->name }}
        </a>
    @endforeach
</nav>