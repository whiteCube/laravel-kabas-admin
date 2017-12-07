<nav class="nav">
    <h2 class="nav__title">Main navigation</h2>
    <a class="nav__link nav__link--primary {{ url()->current() == route('kabas.admin') ? 'nav__link--active' : '' }}" href="{{ route('kabas.admin') }}">Administration</a>
    <em class="nav__subtitle">Pages</em>
    @foreach (Admin::pages() as $page)
        <a class="nav__link {{ url()->current() == route('kabas.admin.page', ['file' => $page->url]) ? 'nav__link--active' : '' }}" href="{{ route('kabas.admin.page', ['file' => $page->url]) }}">
            {{ $page->name }}
        </a>
    @endforeach
    <em class="nav__subtitle">Modèles</em>
    @foreach (Admin::models() as $model)
        <a class="nav__link {{ url()->current() == route('kabas.admin.model', ['file' => $model->file]) ? 'nav__link--active' : '' }}" href="{{ route('kabas.admin.model', ['file' => $model->file]) }}">
            {{ $model->name }}
        </a>
    @endforeach
</nav>