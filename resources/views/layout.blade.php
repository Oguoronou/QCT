<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'QCT') }} — Retrouvez ce qui compte</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body class="font-['Inter'] bg-slate-900 text-slate-50 min-h-screen antialiased">

    <!-- ── NAVBAR ───────────────────────────────────── -->
    <nav class="sticky top-0 z-[100] bg-slate-900/85 backdrop-blur-md border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-6">

            <!-- Logo -->
            <a class="flex items-center gap-2.5 no-underline shrink-0" href="{{ url('/') }}">
                @if (\App\Models\Setting::get('site_logo'))
                    <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'QCT') }}" class="w-9 h-9 rounded-[10px] object-cover">
                @else
                    <div class="w-9 h-9 bg-blue-500 rounded-[10px] flex items-center justify-center text-base text-white">
                        <i class="fas fa-search-location"></i>
                    </div>
                @endif
                <span class="text-xl font-extrabold text-white tracking-[-0.5px]">{{ \App\Models\Setting::get('site_name', 'QCT') }}</span>
            </a>

            <!-- Search (desktop) -->
            <div class="flex-1 max-w-[420px] relative hidden md:block">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]"></i>
                <input type="text" placeholder="Rechercher un objet, une personne…"
                       onkeydown="if(event.key==='Enter') window.location='/all-items?q='+encodeURIComponent(this.value)"
                       class="w-full bg-slate-800 border border-slate-700 rounded-full py-2 pl-10 pr-4 text-sm text-slate-50 outline-none transition-colors focus:border-blue-500 placeholder:text-slate-400">
            </div>

            <!-- Links -->
            <ul class="hidden md:flex items-center gap-2 list-none" id="navLinks">
                <li><a href="{{ url('/all-items') }}" class="text-slate-400 no-underline text-sm font-medium px-3 py-1.5 rounded-lg transition-colors hover:text-slate-50 hover:bg-slate-800 whitespace-nowrap"><i class="fas fa-th mr-1"></i> Explorer</a></li>
                <li><a href="{{ url('/all-items?category=Personnes') }}" class="text-amber-400 no-underline text-sm font-medium px-3 py-1.5 rounded-lg transition-colors hover:text-amber-300 hover:bg-slate-800 whitespace-nowrap"><i class="fas fa-exclamation-circle mr-1"></i> Disparus</a></li>
                <li><a href="{{ url('add-item') }}" class="text-blue-500 no-underline text-sm font-medium px-3 py-1.5 rounded-lg transition-colors hover:text-blue-400 hover:bg-slate-800 whitespace-nowrap"><i class="fas fa-plus mr-1"></i> Signaler</a></li>

                @guest
                    <li><a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-semibold no-underline border bg-transparent text-slate-50 border-slate-700 hover:bg-slate-800 transition-all">Connexion</a></li>
                    @if (Route::has('register'))
                    <li><a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-semibold no-underline bg-blue-500 text-white hover:bg-blue-600 transition-all">Inscription</a></li>
                    @endif
                @else
                    <li class="relative">
                        <button onclick="toggleDropdown(event)" 
                                class="flex items-center gap-2 cursor-pointer px-2 py-1 rounded-full border border-slate-700 bg-slate-800 transition-colors hover:border-blue-500 font-sans text-slate-50">
                            <div class="w-7 h-7 rounded-full bg-blue-500 flex items-center justify-center text-xs font-bold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-[13px] font-semibold text-slate-50 max-w-[100px] overflow-hidden text-ellipsis whitespace-nowrap">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" id="dropdownArrow"></i>
                        </button>
                        <div id="userDropdown" 
                             class="hidden absolute right-0 top-[calc(100%+8px)] bg-slate-800 border border-slate-700 rounded-xl min-w-[180px] shadow-[0_4px_24px_rgba(0,0,0,.35)] overflow-hidden z-[200]">
                            <a href="{{ url('/my-account') }}" class="block w-full text-left px-4 py-2.5 text-sm text-slate-400 no-underline bg-transparent border-none cursor-pointer transition-colors hover:bg-blue-500/10 hover:text-slate-50"><i class="fas fa-user-circle mr-2"></i> Mon profil</a>
                            <a href="{{ url('/my-items') }}" class="block w-full text-left px-4 py-2.5 text-sm text-slate-400 no-underline bg-transparent border-none cursor-pointer transition-colors hover:bg-blue-500/10 hover:text-slate-50"><i class="fas fa-list mr-2"></i> Mes annonces</a>
                            <a href="{{ route('logout') }}" class="block w-full text-left px-4 py-2.5 text-sm text-red-400 no-underline bg-transparent border-none cursor-pointer transition-colors hover:bg-red-500/10 hover:text-red-300"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                        </div>
                    </li>
                @endguest
            </ul>

            <!-- Mobile toggle -->
            <button class="hidden max-md:flex items-center justify-center w-9 h-9 bg-slate-800 border border-slate-700 rounded-lg cursor-pointer text-slate-50" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>

        </div>
    </nav>

    <!-- ── MAIN ─────────────────────────────────────── -->
    <main class="min-h-[calc(100vh-64px)]">
        @yield('content')
    </main>

    <!-- ── FOOTER ───────────────────────────────────── -->
    <footer class="bg-slate-800 border-t border-slate-700 pt-12 pb-6 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-[2fr_1fr_1fr_1fr] max-lg:grid-cols-2 max-sm:grid-cols-1 gap-10 mb-10">

                <div>
                    <a class="flex items-center gap-2.5 no-underline shrink-0 mb-0" href="{{ url('/') }}">
                        @if (\App\Models\Setting::get('site_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('site_logo')) }}" alt="{{ \App\Models\Setting::get('site_name', 'QCT') }}" class="w-9 h-9 rounded-[10px] object-cover">
                        @else
                            <div class="w-9 h-9 bg-blue-500 rounded-[10px] flex items-center justify-center text-base text-white">
                                <i class="fas fa-search-location"></i>
                            </div>
                        @endif
                        <span class="text-xl font-extrabold text-white tracking-[-0.5px]">{{ \App\Models\Setting::get('site_name', 'QCT') }}</span>
                    </a>
                    <p class="text-sm text-slate-400 leading-relaxed mt-3 max-w-[280px]">
                        {{ \App\Models\Setting::get('site_description', "La plateforme communautaire qui connecte objets perdus et propriétaires, et aide à retrouver les personnes disparues en Côte d'Ivoire.") }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-[1px] text-slate-400 mb-4">Plateforme</p>
                    <ul class="list-none flex flex-col gap-2.5">
                        <li><a href="{{ url('/all-items') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Explorer les annonces</a></li>
                        <li><a href="{{ url('add-item') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Signaler une perte</a></li>
                        <li><a href="{{ url('add-found-item') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Signaler une trouvaille</a></li>
                        <li><a href="{{ url('/all-items?category=Personnes') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Personnes disparues</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-[1px] text-slate-400 mb-4">Aide</p>
                    <ul class="list-none flex flex-col gap-2.5">
                        <li><a href="{{ url('/page/comment-ca-marche') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Comment ça marche</a></li>
                        <li><a href="{{ url('/faq') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">FAQ</a></li>
                        <li><a href="{{ url('/page/politique-confidentialite') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Politique de confidentialité</a></li>
                        <li><a href="{{ url('/page/cgu') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Conditions d'utilisation</a></li>
                        <li><a href="{{ url('/page/cgv') }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50">Conditions générales de vente</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-[1px] text-slate-400 mb-4">Contact</p>
                    <ul class="list-none flex flex-col gap-2.5">
                        @php $contactEmail = \App\Models\Setting::get('contact_email', 'contact@qct.ci'); @endphp
                        @if($contactEmail)
                        <li><a href="mailto:{{ $contactEmail }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50"><i class="fas fa-envelope mr-1"></i> {{ $contactEmail }}</a></li>
                        @endif
                        @php $contactPhone = \App\Models\Setting::get('contact_phone', '+225 07 00 00 00 00'); @endphp
                        @if($contactPhone)
                        <li><a href="tel:{{ $contactPhone }}" class="text-slate-400 no-underline text-sm transition-colors hover:text-slate-50"><i class="fas fa-phone mr-1"></i> {{ $contactPhone }}</a></li>
                        @endif
                        @php $contactAddress = \App\Models\Setting::get('contact_address', 'Plateau, Abidjan'); @endphp
                        @if($contactAddress)
                        <li><span class="text-slate-400 text-sm"><i class="fas fa-map-marker-alt mr-1"></i> {{ $contactAddress }}</span></li>
                        @endif
                    </ul>
                </div>

            </div>

            <div class="border-t border-slate-700 pt-6 flex items-center justify-between flex-wrap gap-3">
                <p class="text-[13px] text-slate-400">&copy; {{ date('Y') }} QCT — Tous droits réservés.</p>
                <div class="flex gap-2">
                    @if (\App\Models\Setting::get('social_facebook'))
                    <a href="{{ \App\Models\Setting::get('social_facebook') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('social_twitter'))
                    <a href="{{ \App\Models\Setting::get('social_twitter') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('social_instagram'))
                    <a href="{{ \App\Models\Setting::get('social_instagram') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if (\App\Models\Setting::get('social_whatsapp'))
                    <a href="{{ \App\Models\Setting::get('social_whatsapp') }}" target="_blank" rel="noopener" class="w-[34px] h-[34px] rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-400 text-[13px] no-underline transition-all hover:text-blue-500 hover:border-blue-500"><i class="fab fa-whatsapp"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <script>
        // ── Gestion du dropdown utilisateur au clic ──
        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            const arrow = document.getElementById('dropdownArrow');
            
            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }

        // Fermer le dropdown quand on clique ailleurs
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const arrow = document.getElementById('dropdownArrow');
            
            if (!dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });

        // Empêcher la fermeture quand on clique dans le dropdown
        document.getElementById('userDropdown').addEventListener('click', function(event) {
            event.stopPropagation();
        });

        // ── Image de secours quand une photo ne charge pas ──
        function imgFallback(img) {
            img.onerror = null;
            const fallback = document.createElement('div');
            fallback.className = img.className + ' flex items-center justify-center bg-slate-800 text-slate-500';
            img.replaceWith(fallback);
            const icon = document.createElement('i');
            icon.className = 'fas fa-image';
            fallback.appendChild(icon);
            icon.style.fontSize = Math.max(12, Math.min(fallback.offsetWidth, fallback.offsetHeight) * 0.35) + 'px';
        }

        // ── Gestion du menu mobile ──
        function toggleMobileMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('hidden');
            navLinks.classList.toggle('flex');
            navLinks.classList.toggle('flex-col');
            navLinks.classList.toggle('absolute');
            navLinks.classList.toggle('top-16');
            navLinks.classList.toggle('left-0');
            navLinks.classList.toggle('right-0');
            navLinks.classList.toggle('bg-slate-900');
            navLinks.classList.toggle('border-b');
            navLinks.classList.toggle('border-slate-700');
            navLinks.classList.toggle('p-3');
            navLinks.classList.toggle('gap-1');
        }
    </script>

    {{-- <script src="https://cdn.cinetpay.com/seamless/main.js"></script> --}}
</body>
</html>