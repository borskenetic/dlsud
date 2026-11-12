<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DLSU-D Library</title>
    <link rel="icon" type="image/png" href="{{ asset('img/dlsud-favicon.png') }}">
    <link href="{{ asset('vendor/fontsource/poppins/latin-400.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontsource/poppins/latin-600.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontsource/poppins/latin-700.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset(config('branding.css_path')) }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}?v={{ filemtime(public_path('style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/auth-modal.css') }}?v=6">
</head>
<body>

    <header class="navbar navbar--hero">
        <div class="navbar-content">
            <div class="logo-section">
                <img src="{{ asset('img/dlsud-seal.png') }}" alt="De La Salle University – Dasmariñas seal" class="logo">
                <span class="university-name">
                    DE LA SALLE<br>UNIVERSITY–DASMARIÑAS
                </span>
            </div>
            <button type="button" class="nav-menu-toggle" id="navMenuToggle" aria-expanded="false" aria-controls="primaryNav" aria-label="Open menu">
                <span class="nav-menu-toggle-bar" aria-hidden="true"></span>
                <span class="nav-menu-toggle-bar" aria-hidden="true"></span>
                <span class="nav-menu-toggle-bar" aria-hidden="true"></span>
            </button>
            <nav class="nav-links" id="primaryNav">
                <a href="{{ url('/') }}">HOME</a>
                <a href="{{ asset('about.html') }}">ABOUT</a>
                <a href="{{ route('landing') }}">OPAC</a>
                <a href="{{ asset('contact.html') }}">CONTACT US</a>
                <a href="{{ url('/rooms/book') }}">ROOM RESERVATIONS</a>
                <a href="{{ route('feedback.create') }}" class="feedback-link">FEEDBACK</a>
                <a href="{{ route('login') }}" class="login-button" data-auth-open="login">LOGIN</a>
            </nav>
        </div>
    </header>

    <main class="hero-section">
        <section class="hero-video-feature" aria-label="De La Salle University – Dasmariñas">
            <img src="{{ asset('images/dlsud-banner.jpg') }}" alt="" class="bg-video">
        </section>

        <section class="overlay hero-search-feature" aria-label="OPAC book search">
            <h1 class="welcome-text" hidden>WELCOME TO DLSU-D LIBRARY</h1>
            <div class="home-search-react-root">
                <section class="home-search-panel" aria-labelledby="home-search-title">
                    <p class="home-search-kicker">Online Public Access Catalog</p>
                    <h1 id="home-search-title" class="home-search-title">
                        Search the DLSU-D Library collection
                    </h1>
                    <p class="home-search-copy">
                        Find books by title, author, subject, ISBN, or keyword.
                    </p>

                    <form action="{{ route('landing') }}" method="GET" class="home-opac-search" role="search">
                        <input type="hidden" name="view" value="books">
                        <svg class="home-opac-search__icon" aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                        <input
                            type="search"
                            name="search"
                            placeholder="Search books, authors, titles..."
                            class="home-opac-search__input"
                            aria-label="Search library catalog"
                            required
                        >
                        <button type="submit" class="home-opac-search__button">Search OPAC</button>
                    </form>
                </section>
            </div>
        </section>
    </main>

    <section class="vm-section">
        <div class="gallery-header">
            <h2>De La Salle University – Dasmariñas</h2>
        </div>
        <div class="keplrc-description">
            <p>De La Salle University – Dasmariñas (DLSU-D) is a Lasallian university in Brgy. Fatima 1, Dasmariñas City, Cavite. The seal carries the motto Faith, Service, and Communion. Learn more at <a href="https://www.dlsud.edu.ph/">dlsud.edu.ph</a>.</p>
            <p>This portal is the university library and attendance system. Search the catalog, register for library access, record attendance, and request a room.</p>
        </div>
    </section>

    <footer class="site-footer">
        <div class="footer-content">
            <img src="{{ asset('img/dlsud-seal.png') }}" alt="DLSU-D seal" class="footer-logo">
            <h2 class="footer-university-name">DE LA SALLE UNIVERSITY–DASMARIÑAS</h2>
            <p class="footer-tagline">Dasmariñas City, Cavite</p>
            <p class="footer-tagline">Creating Possibilities</p>
            <p class="footer-tagline"><a href="https://www.dlsud.edu.ph/">www.dlsud.edu.ph</a></p>
            <p class="footer-copyright">DLSU-D Library &copy; {{ date('Y') }}</p>
        </div>
    </footer>

    @include('partials.auth-modal')

    <script src="{{ asset('script.js') }}?v={{ filemtime(public_path('script.js')) }}"></script>
    <script src="{{ asset('js/auth-modal.js') }}?v=4"></script>
</body>
</html>
