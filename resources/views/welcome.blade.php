@extends('layout')

@section('content')

{{-- ════════════════════════════════════════
    HERO — Barre de recherche centrale
════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('3.png') }}" alt="" aria-hidden="true" class="w-full h-full object-cover opacity-30 grayscale-[40%]">
        <div class="absolute inset-0 bg-gradient-to-br from-[rgba(15,23,42,.97)] via-[rgba(15,23,42,.8)] to-[rgba(30,41,59,.7)]"></div>
    </div>

    <div class="relative z-10 container mx-auto px-6 py-20 flex flex-col gap-6 max-w-[820px]">
        <div class="flex">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/25">
                <i class="fas fa-circle text-[7px]"></i>
                {{ $persons->where('status','lost')->count() }} personnes disparues actives
            </span>
        </div>

        <h1 class="text-[clamp(36px,6vw,72px)] font-black leading-[1.05] tracking-[-2px] text-slate-50">
            Retrouvez ce qui<br>
            <span class="text-blue-500 relative">compte vraiment</span>
        </h1>

        <p class="text-[clamp(15px,2vw,18px)] text-slate-400 max-w-[540px] leading-relaxed">
            Signalez, cherchez, retrouvez — objets perdus et personnes disparues en Côte d'Ivoire.
        </p>

        <!-- Barre de recherche principale -->
        <div class="flex flex-col gap-3.5 max-w-[700px]">
            <div class="flex items-center bg-slate-800 border border-slate-700 rounded-full overflow-hidden transition-colors focus-within:border-blue-500 focus-within:shadow-[0_0_0_4px_rgba(59,130,246,.15)]">
                <i class="fas fa-search px-4 text-slate-400 text-sm shrink-0"></i>
                <input type="text" id="heroSearch"
                       placeholder="Que cherchez-vous ? Portefeuille, téléphone, personne…"
                       onkeydown="if(event.key==='Enter') window.location='/all-items?q='+encodeURIComponent(this.value)"
                       class="flex-1 bg-transparent border-none outline-none text-slate-50 text-[15px] font-sans py-4 px-0 placeholder:text-slate-400">
                <button onclick="window.location='/all-items?q='+encodeURIComponent(document.getElementById('heroSearch').value)"
                        class="bg-blue-500 text-white border-none py-3.5 px-7 font-bold text-sm cursor-pointer font-sans transition-colors hover:bg-blue-600 whitespace-nowrap">
                    Rechercher
                </button>
            </div>

            <!-- Catégories rapides -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ url('/all-items?category=Documents') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[13px] font-medium text-slate-400 bg-white/5 border border-slate-700 no-underline transition-all hover:text-slate-50 hover:border-blue-500 hover:bg-blue-500/10">
                    <i class="fas fa-id-card"></i> Documents
                </a>
                <a href="{{ url('/all-items?category=Téléphones') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[13px] font-medium text-slate-400 bg-white/5 border border-slate-700 no-underline transition-all hover:text-slate-50 hover:border-blue-500 hover:bg-blue-500/10">
                    <i class="fas fa-mobile-alt"></i> Téléphones
                </a>
                <a href="{{ url('/all-items?category=Clés') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[13px] font-medium text-slate-400 bg-white/5 border border-slate-700 no-underline transition-all hover:text-slate-50 hover:border-blue-500 hover:bg-blue-500/10">
                    <i class="fas fa-key"></i> Clés
                </a>
                <a href="{{ url('/all-items?category=Portefeuilles') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[13px] font-medium text-slate-400 bg-white/5 border border-slate-700 no-underline transition-all hover:text-slate-50 hover:border-blue-500 hover:bg-blue-500/10">
                    <i class="fas fa-wallet"></i> Portefeuilles
                </a>
                <a href="{{ url('/all-items?category=Animaux') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[13px] font-medium text-slate-400 bg-white/5 border border-slate-700 no-underline transition-all hover:text-slate-50 hover:border-blue-500 hover:bg-blue-500/10">
                    <i class="fas fa-paw"></i> Animaux
                </a>
                <a href="{{ url('/all-items?category=Personnes') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[13px] font-medium text-amber-400 border border-amber-500/30 bg-amber-500/8 no-underline transition-all hover:border-amber-500 hover:bg-amber-500/15">
                    <i class="fas fa-user-slash"></i> Disparus
                </a>
            </div>
        </div>

        <!-- CTAs secondaires -->
        <div class="flex gap-3 flex-wrap">
            <a href="{{ url('add-item') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-[15px] font-semibold cursor-pointer transition-all no-underline border bg-transparent text-slate-50 border-slate-700 hover:bg-slate-800">
                <i class="fas fa-exclamation-triangle"></i> J'ai perdu quelque chose
            </a>
            <a href="{{ url('add-found-item') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-[15px] font-semibold cursor-pointer transition-all no-underline border-none bg-emerald-500 text-white hover:bg-emerald-600">
                <i class="fas fa-hands-helping"></i> J'ai trouvé un objet
            </a>
        </div>
    </div>

    <a href="#recent" class="absolute bottom-8 left-1/2 -translate-x-1/2 w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-slate-400 no-underline animate-bounce z-10">
        <i class="fas fa-chevron-down"></i>
    </a>
</section>

{{-- ════════════════════════════════════════
    STATS BAR
════════════════════════════════════════ --}}
<div class="relative bg-slate-800 border-y border-slate-700 py-10 px-6 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 via-transparent to-emerald-500/5 pointer-events-none"></div>
    <div class="container relative mx-auto px-6">
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-5">
            <div class="group flex flex-col gap-3 bg-slate-900 border border-slate-700 rounded-2xl p-5 transition-all hover:border-emerald-500 hover:-translate-y-1">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center text-base transition-transform group-hover:scale-110">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <span class="text-[26px] font-extrabold text-slate-50 tracking-[-0.5px] leading-none">{{ $stats['resolved_total'] }}</span>
                <span class="text-xs text-slate-400 leading-snug">Retrouvailles réussies</span>
            </div>
            <div class="group flex flex-col gap-3 bg-slate-900 border border-slate-700 rounded-2xl p-5 transition-all hover:border-blue-500 hover:-translate-y-1">
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-500 flex items-center justify-center text-base transition-transform group-hover:scale-110">
                    <i class="fas fa-box-open"></i>
                </div>
                <span class="text-[26px] font-extrabold text-slate-50 tracking-[-0.5px] leading-none">{{ $stats['objects_resolved'] }}</span>
                <span class="text-xs text-slate-400 leading-snug">Objets rendus</span>
            </div>
            <div class="group flex flex-col gap-3 bg-slate-900 border border-slate-700 rounded-2xl p-5 transition-all hover:border-amber-500 hover:-translate-y-1">
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center text-base transition-transform group-hover:scale-110">
                    <i class="fas fa-user-check"></i>
                </div>
                <span class="text-[26px] font-extrabold text-slate-50 tracking-[-0.5px] leading-none">{{ $stats['persons_found'] }}</span>
                <span class="text-xs text-slate-400 leading-snug">Personnes retrouvées</span>
            </div>
            <div class="group flex flex-col gap-3 bg-slate-900 border border-slate-700 rounded-2xl p-5 transition-all hover:border-blue-500 hover:-translate-y-1">
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-500 flex items-center justify-center text-base transition-transform group-hover:scale-110">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <span class="text-[26px] font-extrabold text-slate-50 tracking-[-0.5px] leading-none">{{ $stats['active_listings'] }}</span>
                <span class="text-xs text-slate-400 leading-snug">Annonces actives</span>
            </div>
            <div class="group flex flex-col gap-3 bg-slate-900 border border-slate-700 rounded-2xl p-5 transition-all hover:border-emerald-500 hover:-translate-y-1">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center text-base transition-transform group-hover:scale-110">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-[26px] font-extrabold text-slate-50 tracking-[-0.5px] leading-none">{{ $stats['members'] }}</span>
                <span class="text-xs text-slate-400 leading-snug">Membres inscrits</span>
            </div>
        </div>

        <div class="flex items-center justify-center flex-wrap gap-x-8 gap-y-2 mt-6 pt-6 border-t border-slate-700 text-xs text-slate-400">
            <span class="inline-flex items-center gap-2">
                <i class="fas fa-chart-line text-emerald-500"></i>
                Taux de réussite <strong class="text-slate-50">{{ $stats['success_rate'] }}%</strong>
            </span>
            <span class="inline-flex items-center gap-2">
                <i class="fas fa-calendar-check text-blue-500"></i>
                <strong class="text-slate-50">{{ $stats['resolved_this_month'] }}</strong> résolu(s) ce mois-ci
            </span>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════
    PERSONNES DISPARUES — Urgence
════════════════════════════════════════ --}}
<section class="py-20 bg-slate-900" id="disparus">
    <div class="container mx-auto px-6">
        <div class="flex flex-col gap-3 mb-12">
            <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3"><i class="fas fa-exclamation-circle mr-1"></i> Urgent</p>
            <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Personnes disparues</h2>
            <div class="w-10 h-[3px] bg-red-500 rounded-sm"></div>
            <p class="text-base text-slate-400 leading-relaxed max-w-[560px]">
                Votre aide peut sauver des vies. Si vous avez la moindre information, signalez-la immédiatement.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:gap-5 sm:grid-cols-[repeat(auto-fill,minmax(280px,1fr))]">
            @foreach ($persons as $key => $person)
                @if ($key < 6)
                <a href="{{ url('item-detail', $person->id) }}" class="bg-slate-800 border border-slate-700 rounded-[20px] overflow-hidden no-underline flex flex-col transition-all hover:border-red-500 hover:-translate-y-[3px] hover:shadow-[0_12px_32px_rgba(239,68,68,.12)]">
                    <div class="relative h-[130px] sm:h-[220px] overflow-hidden">
                        <img src="{{ asset(explode(',', $person->images)[0]) }}"
                             alt="{{ $person->item_name }}"
                             loading="lazy"
                             onerror="imgFallback(this)"
                             class="w-full h-full object-cover transition-transform duration-400 group-hover:scale-[1.04]">
                        <span class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $person->status == 'lost' ? 'bg-red-500/15 text-red-300 border border-red-500/25' : 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/25' }}">
                            <i class="fas fa-{{ $person->status == 'lost' ? 'search' : 'check' }}"></i>
                            {{ $person->status == 'lost' ? 'Disparu(e)' : 'Retrouvé(e)' }}
                        </span>
                        @if ($person->status == 'lost')
                        <div class="absolute bottom-3 left-3 w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse shadow-[0_0_0_0_rgba(239,68,68,.5)]"></div>
                        @endif
                    </div>
                    <div class="p-2.5 sm:p-[18px] flex-1 flex flex-col gap-1.5 sm:gap-2">
                        <h3 class="text-sm sm:text-base font-bold text-slate-50">{{ $person->item_name }}</h3>
                        <p class="text-[11px] sm:text-[13px] text-slate-400 leading-relaxed flex-1">{{ Str::limit($person->description, 90) }}</p>
                        <div class="flex items-center justify-between text-[10px] sm:text-xs text-slate-400 mt-1">
                            <span><i class="fas fa-clock mr-1"></i>{{ $person->created_at->diffForHumans(['locale' => 'fr']) }}</span>
                            <span class="text-blue-500 font-semibold">Voir <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
                @endif
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ url('/all-items?category=Personnes') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-semibold cursor-pointer transition-all no-underline border bg-transparent text-slate-50 border-slate-700 hover:bg-slate-800">
                <i class="fas fa-list"></i> Voir toutes les disparitions
            </a>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
    ANNONCES RÉCENTES
════════════════════════════════════════ --}}
<section class="py-20 bg-[#0D1525]" id="recent">
    <div class="container mx-auto px-6">
        <div class="flex flex-row justify-between items-end flex-wrap gap-4 mb-12">
            <div class="flex flex-col gap-3">
                <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3">Récent</p>
                <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Objets signalés</h2>
                <div class="w-10 h-[3px] bg-blue-500 rounded-sm"></div>
            </div>
            <a href="{{ url('/all-items') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-[13px] font-semibold cursor-pointer transition-all no-underline border bg-transparent text-slate-50 border-slate-700 hover:bg-slate-800">
                Tout voir <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Filtres catégories -->
        <div class="flex overflow-x-auto gap-2 pb-1 mb-8 scrollbar-none" id="catTabs">
            <button class="cat-tab inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-medium bg-slate-800 border border-slate-700 text-slate-400 cursor-pointer whitespace-nowrap transition-all font-sans hover:bg-blue-500 hover:border-blue-500 hover:text-white active bg-blue-500 border-blue-500 text-white" data-cat="all">Tous</button>
            <button class="cat-tab inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-medium bg-slate-800 border border-slate-700 text-slate-400 cursor-pointer whitespace-nowrap transition-all font-sans hover:bg-blue-500 hover:border-blue-500 hover:text-white" data-cat="Documents"><i class="fas fa-id-card mr-1"></i> Documents</button>
            <button class="cat-tab inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-medium bg-slate-800 border border-slate-700 text-slate-400 cursor-pointer whitespace-nowrap transition-all font-sans hover:bg-blue-500 hover:border-blue-500 hover:text-white" data-cat="Téléphones"><i class="fas fa-mobile-alt mr-1"></i> Téléphones</button>
            <button class="cat-tab inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-medium bg-slate-800 border border-slate-700 text-slate-400 cursor-pointer whitespace-nowrap transition-all font-sans hover:bg-blue-500 hover:border-blue-500 hover:text-white" data-cat="Clés"><i class="fas fa-key mr-1"></i> Clés</button>
            <button class="cat-tab inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-medium bg-slate-800 border border-slate-700 text-slate-400 cursor-pointer whitespace-nowrap transition-all font-sans hover:bg-blue-500 hover:border-blue-500 hover:text-white" data-cat="Portefeuilles"><i class="fas fa-wallet mr-1"></i> Portefeuilles</button>
            <button class="cat-tab inline-flex items-center gap-1.5 px-[18px] py-2 rounded-full text-[13px] font-medium bg-slate-800 border border-slate-700 text-slate-400 cursor-pointer whitespace-nowrap transition-all font-sans hover:bg-blue-500 hover:border-blue-500 hover:text-white" data-cat="Animaux"><i class="fas fa-paw mr-1"></i> Animaux</button>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:gap-5 sm:grid-cols-[repeat(auto-fill,minmax(280px,1fr))]" id="itemsGrid">
            @foreach ($items as $item)
            <a href="{{ url('item-detail', $item->id) }}"
               class="item-card bg-slate-800 border border-slate-700 rounded-[20px] overflow-hidden no-underline flex flex-col transition-all hover:border-blue-500 hover:-translate-y-[3px] hover:shadow-[0_12px_32px_rgba(59,130,246,.15)]"
               data-cat="{{ $item->category_name }}">
                <div class="relative h-[120px] sm:h-[200px] overflow-hidden">
                    <img src="{{ asset(explode(',', $item->images)[0]) }}"
                         alt="{{ $item->item_name }}"
                         loading="lazy"
                         onerror="imgFallback(this)"
                         class="w-full h-full object-cover transition-transform duration-400 hover:scale-[1.04]">
                    <span class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $item->status == 'lost' ? 'bg-red-500/15 text-red-300 border border-red-500/25' : 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/25' }}">
                        {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
                    </span>
                </div>
                <div class="p-2.5 sm:p-4 flex-1 flex flex-col gap-1 sm:gap-1.5">
                    <div class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-[1px] text-blue-500 flex items-center gap-1.5">
                        <i class="fas fa-{{ $item->category_name == 'Documents' ? 'file-alt' : ($item->category_name == 'Téléphones' ? 'mobile-alt' : ($item->category_name == 'Clés' ? 'key' : 'box')) }}"></i>
                        {{ $item->category_name }}
                    </div>
                    <h3 class="text-[13px] sm:text-[15px] font-bold text-slate-50">{{ $item->item_name }}</h3>
                    <p class="text-[11px] sm:text-[13px] text-slate-400 leading-relaxed flex-1">{{ Str::limit($item->description, 80) }}</p>
                    <div class="flex items-center justify-between text-[10px] sm:text-xs text-slate-400 mt-1 sm:mt-1.5">
                        <span><i class="fas fa-clock mr-1"></i>{{ $item->created_at->diffForHumans(['locale' => 'fr']) }}</span>
                        <span class="text-blue-500 font-semibold">Voir <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
    COMMENT ÇA MARCHE
════════════════════════════════════════ --}}
<section class="py-20 bg-slate-800 border-y border-slate-700" id="how-it-works">
    <div class="container mx-auto px-6">
        <div class="flex flex-col items-center text-center gap-3 mb-12">
            <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3">Fonctionnement</p>
            <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Simple, rapide, efficace</h2>
            <div class="w-10 h-[3px] bg-blue-500 rounded-sm mx-auto"></div>
            <p class="text-base text-slate-400 leading-relaxed max-w-[560px] mx-auto">
                Trois étapes pour réunir objets perdus et propriétaires grâce à la force de la communauté.
            </p>
        </div>

        <div class="grid grid-cols-[minmax(280px,420px)_1fr] max-lg:grid-cols-1 gap-10 lg:gap-14 items-center">
            <div class="relative rounded-[24px] overflow-hidden max-lg:order-2">
                <img src="{{ asset('1.png') }}" alt="Objet retrouvé sur un banc" class="w-full h-[280px] lg:h-[420px] object-cover" loading="lazy" onerror="imgFallback(this)">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/15 to-transparent"></div>
                <div class="absolute bottom-5 left-5 right-5 flex items-center gap-3 bg-slate-900/80 backdrop-blur-sm border border-slate-700 rounded-2xl p-4">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <div>
                        <p class="text-xl font-extrabold text-slate-50 leading-none">{{ $stats['resolved_total'] }}</p>
                        <p class="text-xs text-slate-400 mt-1">retrouvailles réussies grâce à la communauté</p>
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col gap-6 max-lg:order-1">
                <div class="absolute left-6 top-4 bottom-4 w-px bg-slate-700 max-sm:hidden"></div>

                <div class="relative flex gap-5 items-start">
                    <div class="shrink-0 z-10 w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 text-blue-500 flex items-center justify-center text-xl">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="pt-1.5">
                        <div class="text-[11px] font-extrabold tracking-[2px] text-slate-400 uppercase mb-1">Étape 01</div>
                        <h3 class="text-lg font-bold text-slate-50 mb-1">Signalez</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Créez une annonce avec photos et description précise pour maximiser vos chances.</p>
                    </div>
                </div>

                <div class="relative flex gap-5 items-start">
                    <div class="shrink-0 z-10 w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 text-emerald-500 flex items-center justify-center text-xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="pt-1.5">
                        <div class="text-[11px] font-extrabold tracking-[2px] text-slate-400 uppercase mb-1">Étape 02</div>
                        <h3 class="text-lg font-bold text-slate-50 mb-1">La communauté agit</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Des milliers de membres reçoivent des alertes et partagent les informations.</p>
                    </div>
                </div>

                <div class="relative flex gap-5 items-start">
                    <div class="shrink-0 z-10 w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 text-amber-500 flex items-center justify-center text-xl">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="pt-1.5">
                        <div class="text-[11px] font-extrabold tracking-[2px] text-slate-400 uppercase mb-1">Étape 03</div>
                        <h3 class="text-lg font-bold text-slate-50 mb-1">Retrouvailles</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">Mise en contact sécurisée et vérification pour des retrouvailles en toute confiance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
    COMMISSARIATS PARTENAIRES — Carte
════════════════════════════════════════ --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<section class="py-20 bg-slate-900" id="commissariats">
    <div class="container mx-auto px-6">
        <div class="flex flex-col items-center text-center gap-3 mb-12">
            <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3"><i class="fas fa-shield-alt mr-1"></i> Réseau officiel</p>
            <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Commissariats partenaires</h2>
            <div class="w-10 h-[3px] bg-blue-500 rounded-sm mx-auto"></div>
            <p class="text-base text-slate-400 leading-relaxed max-w-[560px] mx-auto">
                Déposez ou récupérez un objet trouvé en toute sécurité auprès de nos commissariats partenaires à Abidjan.
            </p>
        </div>

        @if ($commissariats->isEmpty())
        <p class="text-center text-slate-400 text-sm">Aucun commissariat partenaire actif pour le moment.</p>
        @else
        <div class="grid grid-cols-[1fr_320px] max-lg:grid-cols-1 gap-6 items-stretch">
            <div id="commissariatsMap" class="h-[420px] rounded-[20px] border border-slate-700 overflow-hidden z-0"></div>

            <div class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex flex-col gap-1">
                        <span class="text-2xl font-extrabold text-blue-500">{{ $commissariats->count() }}</span>
                        <span class="text-xs text-slate-400">Commissariats actifs</span>
                    </div>
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-4 flex flex-col gap-1">
                        <span class="text-2xl font-extrabold text-emerald-500">{{ $commissariats->pluck('commune')->unique()->count() }}</span>
                        <span class="text-xs text-slate-400">Communes couvertes</span>
                    </div>
                </div>

                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="commissariatSearch" placeholder="Rechercher un commissariat, une commune…"
                           class="w-full bg-slate-800 border border-slate-700 rounded-full py-2.5 pl-9 pr-4 text-[13px] text-slate-50 outline-none transition-colors focus:border-blue-500 placeholder:text-slate-400">
                </div>

                <div class="flex-1 flex flex-col gap-2 bg-slate-800 border border-slate-700 rounded-2xl p-3 max-h-[280px] overflow-y-auto scrollbar-none" id="commissariatsList">
                    @foreach ($commissariats as $c)
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="commissariat-item flex items-center gap-3 text-left flex-1 min-w-0 bg-transparent border border-transparent rounded-xl p-2.5 cursor-pointer transition-colors hover:bg-slate-900 hover:border-slate-700 font-sans" data-lat="{{ $c->lat }}" data-lng="{{ $c->lng }}" data-search="{{ mb_strtolower($c->name.' '.$c->commune) }}">
                            <div class="w-9 h-9 shrink-0 rounded-lg bg-blue-500/15 text-blue-500 flex items-center justify-center text-sm">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[13px] font-semibold text-slate-50 truncate">{{ $c->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $c->commune }}{{ $c->phone ? ' · '.$c->phone : '' }}</p>
                            </div>
                            @unless ($c->precise)
                            <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-amber-500" title="Position approximative (commune)"></span>
                            @endunless
                        </button>
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $c->lat }},{{ $c->lng }}&travelmode=driving"
                           target="_blank" rel="noopener"
                           class="shrink-0 w-9 h-9 rounded-lg bg-slate-900 border border-slate-700 text-blue-500 flex items-center justify-center text-xs no-underline transition-colors hover:bg-blue-500/15 hover:border-blue-500"
                           title="Itinéraire depuis ma position vers ce commissariat">
                            <i class="fas fa-route"></i>
                        </a>
                    </div>
                    @endforeach
                    <p class="hidden text-center text-xs text-slate-400 py-4" id="commissariatNoResults">Aucun commissariat ne correspond à votre recherche.</p>
                </div>

                <a href="{{ url('add-found-item') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full text-sm font-semibold no-underline bg-blue-500 text-white hover:bg-blue-600 transition-all">
                    <i class="fas fa-plus"></i> Signaler une trouvaille
                </a>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- ════════════════════════════════════════
    SUCCÈS RÉCENTS — Carousel
════════════════════════════════════════ --}}
<section class="py-20 bg-slate-900">
    <div class="container mx-auto px-6">
        <div class="flex flex-row justify-between items-end flex-wrap gap-4 mb-12">
            <div class="flex flex-col gap-3">
                <p class="text-xs font-bold uppercase tracking-[1.5px] text-emerald-500 mb-3">
                    <i class="fas fa-check-circle mr-1"></i> Résolus
                </p>
                <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Histoires de retrouvailles</h2>
                <div class="w-10 h-[3px] bg-emerald-500 rounded-sm"></div>
            </div>
            <div class="flex gap-2">
                <button class="carousel-ctrl w-10 h-10 rounded-[10px] bg-slate-800 border border-slate-700 text-slate-50 cursor-pointer text-sm flex items-center justify-center transition-all hover:border-blue-500 hover:bg-blue-500/10 hover:text-blue-500 font-sans" id="carPrev"><i class="fas fa-arrow-left"></i></button>
                <button class="carousel-ctrl w-10 h-10 rounded-[10px] bg-slate-800 border border-slate-700 text-slate-50 cursor-pointer text-sm flex items-center justify-center transition-all hover:border-blue-500 hover:bg-blue-500/10 hover:text-blue-500 font-sans" id="carNext"><i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <div class="overflow-x-auto scrollbar-none snap-x snap-mandatory rounded-[20px]">
            <div class="flex gap-5" id="carTrack">
                @foreach($resolvedItems as $item)
                <div class="success-card snap-start min-w-full sm:min-w-[calc(50%-10px)] lg:min-w-[calc(33.333%-14px)] bg-slate-800 border border-slate-700 rounded-[20px] overflow-hidden shrink-0">
                    <div class="relative h-[180px] overflow-hidden">
                        <img src="{{ asset(explode(',', $item->images)[0]) }}"
                             alt="{{ $item->item_name }}"
                             loading="lazy"
                             onerror="imgFallback(this)"
                             class="w-full h-full object-cover">
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/25">
                            <i class="fas fa-check"></i> Retrouvé
                        </span>
                    </div>
                    <div class="p-[18px] flex flex-col gap-2">
                        <span class="text-[11px] font-bold uppercase tracking-[1px] text-emerald-500">{{ $item->category_name ?? ucfirst($item->category) }}</span>
                        <h3 class="text-[15px] font-bold text-slate-50">{{ $item->item_name }}</h3>
                        <p class="text-[13px] text-slate-400 leading-relaxed">{{ Str::limit($item->description, 80) }}</p>
                        <div class="flex items-center gap-2.5 mt-2 pt-3 border-t border-slate-700">
                            <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-[13px] font-bold text-white shrink-0">
                                {{ strtoupper(substr($item->foundBy->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400">Retrouvé par</p>
                                <p class="text-[13px] font-semibold">{{ $item->foundBy->name ?? 'Anonyme' }}</p>
                            </div>
                            <span class="ml-auto text-xs text-slate-400">
                                {{ $item->updated_at->diffForHumans(['locale' => 'fr']) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
    TÉMOIGNAGES
════════════════════════════════════════ --}}
@if ($testimonials->isNotEmpty())
<section class="relative py-20 bg-[#0D1525] border-t border-slate-700 overflow-hidden">
    <div class="absolute -top-24 -left-24 w-[340px] h-[340px] rounded-full bg-blue-500/10 blur-[90px] pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-[340px] h-[340px] rounded-full bg-emerald-500/10 blur-[90px] pointer-events-none"></div>
    <i class="fas fa-quote-right absolute top-10 right-[6%] text-[140px] text-slate-800/60 pointer-events-none max-md:hidden" aria-hidden="true"></i>

    <div class="container relative mx-auto px-6">
        <div class="flex flex-col items-center text-center gap-3 mb-12">
            <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3">Témoignages</p>
            <h2 class="text-[clamp(28px,4vw,40px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Ils nous font confiance</h2>
            <div class="w-10 h-[3px] bg-blue-500 rounded-sm mx-auto"></div>
        </div>

        <div class="grid grid-cols-[repeat(auto-fill,minmax(280px,1fr))] gap-5">
            @foreach($testimonials as $testimonial)
            <div class="bg-slate-800 border border-slate-700 rounded-[20px] p-7 flex flex-col gap-4 transition-colors hover:border-blue-500">
                <p class="text-sm text-slate-400 leading-relaxed flex-1 italic">"{{ Str::limit($testimonial->message, 200) }}"</p>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-700">
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
                        {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-sm">{{ $testimonial->name }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════
    DON — CinetPay
════════════════════════════════════════ --}}
<section class="py-20 bg-slate-800 border-t border-slate-700">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 max-md:grid-cols-1 gap-[60px] items-start">
            <div class="relative">
                <i class="fas fa-heart absolute -top-6 -left-4 text-[160px] text-blue-500/[0.06] pointer-events-none max-md:hidden" aria-hidden="true"></i>
                <p class="relative text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3">Soutien</p>
                <h2 class="text-[clamp(24px,3vw,34px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Soutenez notre mission</h2>
                <p class="text-[15px] text-slate-400 leading-relaxed max-w-[560px]">
                    Votre don nous aide à maintenir la plateforme et à toucher plus de personnes dans le besoin.
                </p>
                <div class="flex flex-col gap-3 mt-6">
                    <div class="flex items-center gap-2.5 text-sm text-slate-400"><i class="fas fa-check-circle text-emerald-500"></i> Plateforme 100% gratuite pour les utilisateurs</div>
                    <div class="flex items-center gap-2.5 text-sm text-slate-400"><i class="fas fa-check-circle text-emerald-500"></i> Alertes SMS en temps réel</div>
                    <div class="flex items-center gap-2.5 text-sm text-slate-400"><i class="fas fa-check-circle text-emerald-500"></i> Disponible 24h/24 et 7j/7</div>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-700 rounded-[20px] p-7">
                <p class="text-base font-bold mb-5">Faire un don</p>

                <div class="flex gap-2 flex-wrap mb-5">
                    <button class="donate-amt px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-400 text-[13px] font-semibold cursor-pointer font-sans transition-all hover:bg-blue-500/15 hover:border-blue-500 hover:text-blue-500" onclick="setAmt(1000)">1 000 F</button>
                    <button class="donate-amt px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-400 text-[13px] font-semibold cursor-pointer font-sans transition-all hover:bg-blue-500/15 hover:border-blue-500 hover:text-blue-500" onclick="setAmt(2500)">2 500 F</button>
                    <button class="donate-amt px-4 py-2 rounded-lg bg-blue-500/15 border-blue-500 text-blue-500 text-[13px] font-semibold cursor-pointer font-sans transition-all active" onclick="setAmt(5000)">5 000 F</button>
                    <button class="donate-amt px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-400 text-[13px] font-semibold cursor-pointer font-sans transition-all hover:bg-blue-500/15 hover:border-blue-500 hover:text-blue-500" onclick="setAmt(10000)">10 000 F</button>
                </div>

                <div class="flex flex-col gap-3.5 mb-5">
                    <div class="grid grid-cols-2 max-sm:grid-cols-1 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Montant (XOF)</label>
                            <div class="relative">
                                <input type="number" id="amount" value="5000" placeholder="5000"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">FCFA</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Téléphone</label>
                            <input type="text" id="customer_phone_number" placeholder="0701234567"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 max-sm:grid-cols-1 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Nom</label>
                            <input type="text" id="customer_name" placeholder="Votre nom"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Prénom</label>
                            <input type="text" id="customer_surname" placeholder="Votre prénom"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Email</label>
                        <input type="email" id="customer_email" placeholder="email@exemple.com"
                               class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                    </div>
                    <div class="grid grid-cols-2 max-sm:grid-cols-1 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Ville</label>
                            <input type="text" id="customer_city" placeholder="Abidjan"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Pays</label>
                            <select id="customer_country"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)]">
                                <option value="CI">Côte d'Ivoire</option>
                                <option value="BF">Burkina Faso</option>
                                <option value="SN">Sénégal</option>
                                <option value="ML">Mali</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="customer_address" value="Abidjan">
                </div>

                <button onclick="checkout()" class="inline-flex items-center gap-2 px-3.5 py-3.5 rounded-xl text-[15px] font-semibold cursor-pointer transition-all no-underline border-none bg-blue-500 text-white hover:bg-blue-600 w-full justify-center">
                    <i class="fas fa-heart"></i> Faire un don maintenant
                </button>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
    CONTACT
════════════════════════════════════════ --}}
<section class="py-20 bg-slate-900 border-t border-slate-700">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 max-md:grid-cols-1 gap-[60px] items-start">
            <div>
                <p class="text-xs font-bold uppercase tracking-[1.5px] text-blue-500 mb-3">Contact</p>
                <h2 class="text-[clamp(24px,3vw,34px)] font-extrabold text-slate-50 tracking-[-1px] leading-tight">Nous contacter</h2>
                <div class="w-10 h-[3px] bg-blue-500 rounded-sm"></div>
                <p class="text-[15px] text-slate-400 leading-relaxed max-w-[560px] mt-3">Une question ou besoin d'aide ? Notre équipe est disponible pour vous.</p>

                <div class="flex flex-col gap-5 mt-8">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 shrink-0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="font-semibold mb-0.5">Adresse</p>
                            <p class="text-slate-400 text-sm">Plateau, Abidjan, Côte d'Ivoire</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 shrink-0">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <p class="font-semibold mb-0.5">Téléphone</p>
                            <p class="text-slate-400 text-sm">+225 07 00 00 00 00</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="font-semibold mb-0.5">Email</p>
                            <p class="text-slate-400 text-sm">contact@qct.ci</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-500 shrink-0">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="font-semibold mb-0.5">Heures d'ouverture</p>
                            <p class="text-slate-400 text-sm">Lun–Ven : 8h–18h &nbsp;|&nbsp; Sam : 9h–13h</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-800 border border-slate-700 rounded-[20px] p-8">
                @if (Session::has('messages'))
                <div class="p-3.5 mb-5 rounded-[10px] bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>{{ Session::get('messages') }}
                </div>
                @endif

                <p class="text-base font-bold mb-5">Envoyez-nous un message</p>

                <form action="{{ url('contact-us') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Votre nom</label>
                        <input type="text" name="name" placeholder="Nom complet" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Votre email</label>
                        <input type="email" name="email" placeholder="email@exemple.com" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Message</label>
                        <textarea name="message" rows="5" placeholder="Votre message…" required
                                  class="w-full bg-slate-900 border border-slate-700 rounded-[10px] px-3.5 py-[11px] text-sm text-slate-50 font-sans outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-400 resize-y"></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-3.5 rounded-xl text-[15px] font-semibold cursor-pointer transition-all no-underline border-none bg-blue-500 text-white hover:bg-blue-600 justify-center">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Carte des commissariats partenaires
(function() {
    const mapEl = document.getElementById('commissariatsMap');
    if (!mapEl || typeof L === 'undefined') return;

    const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    const commissariats = @json($commissariats);

    const map = L.map(mapEl, { scrollWheelZoom: false }).setView([5.345, -4.02], 11);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    const pinIcon = L.divIcon({
        className: '',
        html: '<div style="width:34px;height:34px;border-radius:50% 50% 50% 0;background:#3b82f6;border:2px solid #fff;transform:rotate(-45deg);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,.4)"><i class="fas fa-shield-alt" style="transform:rotate(45deg);color:#fff;font-size:13px"></i></div>',
        iconSize: [34, 34],
        iconAnchor: [17, 34],
        popupAnchor: [0, -34]
    });

    const markers = commissariats.map((c) => {
        const marker = L.marker([c.lat, c.lng], { icon: pinIcon }).addTo(map);
        const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${c.lat},${c.lng}&travelmode=driving`;
        marker.bindPopup(
            `<div style="font-family:Inter,sans-serif;min-width:170px">
                <strong style="display:block;margin-bottom:4px">${escapeHtml(c.name)}</strong>
                <span style="display:block;color:#64748b;font-size:12px">${escapeHtml(c.commune)}</span>
                ${c.phone ? `<span style="display:block;font-size:12px;margin-top:4px"><i class="fas fa-phone" style="margin-right:4px"></i>${escapeHtml(c.phone)}</span>` : ''}
                <a href="${directionsUrl}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:6px 10px;border-radius:8px;background:#3b82f6;color:#fff;font-size:12px;font-weight:600;text-decoration:none">
                    <i class="fas fa-route"></i> Itinéraire
                </a>
            </div>`
        );
        return marker;
    });

    const items = Array.from(document.querySelectorAll('.commissariat-item'));
    items.forEach((btn, i) => {
        btn.addEventListener('click', () => {
            const lat = parseFloat(btn.dataset.lat);
            const lng = parseFloat(btn.dataset.lng);
            map.setView([lat, lng], 15);
            if (markers[i]) markers[i].openPopup();
        });
    });

    // ── Recherche dans la liste des commissariats
    const searchInput = document.getElementById('commissariatSearch');
    const noResults = document.getElementById('commissariatNoResults');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;
            items.forEach((btn) => {
                const match = btn.dataset.search.includes(q);
                btn.classList.toggle('hidden', !match);
                if (match) visibleCount++;
            });
            if (noResults) noResults.classList.toggle('hidden', visibleCount > 0);
        });
    }
})();

// ── Category filter tabs
document.querySelectorAll('.cat-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active', 'bg-blue-500', 'border-blue-500', 'text-white'));
        btn.classList.add('active', 'bg-blue-500', 'border-blue-500', 'text-white');
        const cat = btn.dataset.cat;
        document.querySelectorAll('.item-card').forEach(card => {
            if (cat === 'all' || card.dataset.cat === cat) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    });
});

// ── Carousel
(function() {
    const track   = document.getElementById('carTrack');
    const prevBtn = document.getElementById('carPrev');
    const nextBtn = document.getElementById('carNext');
    if (!track) return;

    function getCardWidth() {
        const card = track.querySelector('.success-card');
        return card ? card.offsetWidth + 20 : track.clientWidth;
    }

    function scrollByCard(direction) {
        track.scrollBy({ left: direction * getCardWidth(), behavior: 'smooth' });
    }

    function autoAdvance() {
        const atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 5;
        track.scrollTo({ left: atEnd ? 0 : track.scrollLeft + getCardWidth(), behavior: 'smooth' });
    }

    nextBtn.addEventListener('click', () => scrollByCard(1));
    prevBtn.addEventListener('click', () => scrollByCard(-1));

    let autoplay = setInterval(autoAdvance, 5000);
    track.addEventListener('mouseenter', () => clearInterval(autoplay));
    track.addEventListener('mouseleave', () => {
        autoplay = setInterval(autoAdvance, 5000);
    });
})();

// ── Donate amount shortcuts
function setAmt(v) {
    document.getElementById('amount').value = v;
    document.querySelectorAll('.donate-amt').forEach(b => {
        if (parseInt(b.textContent.replace(/\D/g,'')) === v) {
            b.classList.add('active', 'bg-blue-500/15', 'border-blue-500', 'text-blue-500');
        } else {
            b.classList.remove('active', 'bg-blue-500/15', 'border-blue-500', 'text-blue-500');
        }
    });
}

// ── CinetPay checkout
function checkout() {
    CinetPay.setConfig({
        apikey: '{{ env("CINETPAY_API_KEY") }}',
        site_id: '{{ env("CINETPAY_SITE_ID") }}',
        notify_url: '{{ url("/payment/notify") }}',
        mode: 'PRODUCTION'
    });
    CinetPay.getCheckout({
        transaction_id: Math.floor(Math.random() * 100000000).toString(),
        amount: document.getElementById('amount').value,
        currency: 'XOF',
        description: 'Donation pour QCT',
        customer_id: 'user_' + Math.floor(Math.random() * 10000),
        customer_name: document.getElementById('customer_name').value,
        customer_surname: document.getElementById('customer_surname').value,
        customer_email: document.getElementById('customer_email').value,
        customer_phone_number: document.getElementById('customer_phone_number').value,
        customer_address: document.getElementById('customer_address').value,
        customer_city: document.getElementById('customer_city').value,
        customer_country: document.getElementById('customer_country').value,
        customer_state: 'CI',
        customer_zip_code: '',
        channels: 'ALL',
    });
    CinetPay.waitResponse(function(data) {
        const isOk = data.status === 'ACCEPTED';
        alert(isOk ? '✅ Merci pour votre don !' : '❌ Paiement refusé. Veuillez réessayer.');
    });
    CinetPay.onError(function(data) {
        console.error(data);
        alert('Une erreur est survenue lors du paiement.');
    });
}
</script>

<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
    @keyframes bounce {
        0%, 100% { transform: translateY(0) translateX(-50%); }
        50% { transform: translateY(-8px) translateX(-50%); }
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    .animate-bounce {
        animation: bounce 2s infinite;
    }
    .animate-pulse {
        animation: pulse 1.5s infinite;
    }
</style>

@endsection