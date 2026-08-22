@extends('layout')
@section("content")

<div class="min-h-[calc(100vh-64px)] bg-slate-900 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Barre de recherche -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 mb-8 shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <form method="GET" action="{{ route('all-items') }}" class="space-y-4 md:space-y-0 md:grid md:grid-cols-5 md:gap-4">
                <!-- Champ de recherche -->
                <div class="relative md:col-span-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-500 text-sm"></i>
                    </div>
                    <input name="q" 
                           class="w-full pl-10 pr-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500" 
                           type="search" 
                           placeholder="Rechercher un objet..." 
                           value="{{ request()->input('q') }}">
                </div>
                
                <!-- Sélecteur de catégorie -->
                <div>
                    <select name="category" class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] appearance-none cursor-pointer">
                        <option value="" class="bg-slate-800">Toutes catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_name }}" {{ request()->input('category') == $category->category_name ? 'selected' : '' }} class="bg-slate-800">
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sélecteur de statut -->
                <div>
                    <select name="status" class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] appearance-none cursor-pointer">
                        <option value="" class="bg-slate-800">Tous statuts</option>
                        <option value="lost" {{ request()->input('status') == 'lost' ? 'selected' : '' }} class="bg-slate-800">Perdus</option>
                        <option value="found" {{ request()->input('status') == 'found' ? 'selected' : '' }} class="bg-slate-800">Trouvés</option>
                    </select>
                </div>
                
                <!-- Boutons d'action -->
                <div class="grid grid-cols-2 gap-2">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40">
                        <i class="fas fa-search text-sm"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('all-items') }}" class="w-full bg-slate-700 hover:bg-slate-600 text-slate-200 py-3 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-times text-sm"></i>
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Résultats -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête des résultats -->
            <div class="p-6 border-b border-slate-700">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-50">
                            {{ $items->total() }} résultat{{ $items->total() > 1 ? 's' : '' }} trouvé{{ $items->total() > 1 ? 's' : '' }}
                        </h2>
                        @if(request()->has('q') || request()->has('category') || request()->has('status'))
                        <div class="flex flex-wrap items-center gap-2 mt-3">
                            <span class="text-xs text-slate-400">Filtres actifs :</span>
                            @if(request()->input('q')) 
                                <span class="bg-blue-500/15 text-blue-400 text-xs font-medium px-2.5 py-1 rounded-full border border-blue-500/25 flex items-center gap-1.5">
                                    <i class="fas fa-search text-[10px]"></i>
                                    {{ request()->input('q') }}
                                    <a href="{{ route('all-items', array_merge(request()->except('q'), ['page' => null])) }}" class="hover:text-blue-300">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request()->input('category')) 
                                <span class="bg-blue-500/15 text-blue-400 text-xs font-medium px-2.5 py-1 rounded-full border border-blue-500/25 flex items-center gap-1.5">
                                    <i class="fas fa-tag text-[10px]"></i>
                                    {{ request()->input('category') }}
                                    <a href="{{ route('all-items', array_merge(request()->except('category'), ['page' => null])) }}" class="hover:text-blue-300">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if(request()->input('status')) 
                                <span class="bg-{{ request()->input('status') == 'lost' ? 'red' : 'emerald' }}-500/15 text-{{ request()->input('status') == 'lost' ? 'red' : 'emerald' }}-400 text-xs font-medium px-2.5 py-1 rounded-full border border-{{ request()->input('status') == 'lost' ? 'red' : 'emerald' }}-500/25 flex items-center gap-1.5">
                                    <i class="fas fa-{{ request()->input('status') == 'lost' ? 'exclamation-circle' : 'check-circle' }} text-[10px]"></i>
                                    {{ request()->input('status') == 'lost' ? 'Perdus' : 'Trouvés' }}
                                    <a href="{{ route('all-items', array_merge(request()->except('status'), ['page' => null])) }}" class="hover:text-{{ request()->input('status') == 'lost' ? 'red' : 'emerald' }}-300">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            <a href="{{ route('all-items') }}" class="text-xs text-slate-400 hover:text-slate-300 underline ml-2">
                                Effacer tous les filtres
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Liste des résultats -->
            <div class="grid grid-cols-2 gap-3 sm:gap-6 md:grid-cols-2 lg:grid-cols-3 p-3 sm:p-6">
                @forelse ($items as $item)
                <a href="{{ url('item-detail', $item->id) }}" class="bg-slate-900 rounded-2xl overflow-hidden border border-slate-700 hover:border-blue-500 hover:-translate-y-1 transition-all shadow-[0_4px_24px_rgba(0,0,0,.35)] hover:shadow-[0_12px_32px_rgba(59,130,246,.15)] group no-underline">
                    <div class="relative">
                        <img class="w-full h-28 sm:h-64 object-cover transition-transform duration-400 group-hover:scale-[1.04]"
                             src="{{ asset(explode(',', $item->images)[0]) }}"
                             alt="{{ $item->item_name }}"
                             loading="lazy"
                             onerror="imgFallback(this)">
                        <span class="absolute top-2 right-2 sm:top-4 sm:right-4 inline-flex items-center gap-1.5 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold {{ $item->status == 'lost' ? 'bg-red-500/90 text-white' : 'bg-emerald-500/90 text-white' }} backdrop-blur-sm shadow-lg">
                            <i class="fas fa-{{ $item->status == 'lost' ? 'search' : 'check' }} text-[10px]"></i>
                            {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
                        </span>
                    </div>
                    <div class="p-2.5 sm:p-6">
                        <div class="flex items-center mb-2 sm:mb-3">
                            <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-blue-500/15 flex items-center justify-center mr-2 sm:mr-3 shrink-0">
                                <i class="fas fa-{{ $item->category == 'documents' ? 'file-alt' : ($item->category == 'electronics' ? 'mobile-alt' : ($item->category == 'keys' ? 'key' : ($item->category == 'wallet' ? 'wallet' : 'box'))) }} text-blue-500 text-xs sm:text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <h5 class="font-bold text-sm sm:text-lg text-slate-50 truncate">{{ $item->item_name }}</h5>
                                <p class="text-[10px] sm:text-xs text-slate-400">{{ ucfirst($item->category) }}</p>
                            </div>
                        </div>
                        <p class="text-slate-400 text-[11px] sm:text-sm mb-2 sm:mb-4 line-clamp-3">{{ Str::limit($item->description, 100) }}</p>
                        <div class="flex items-center justify-between pt-2 sm:pt-4 border-t border-slate-700">
                            <span class="text-blue-400 font-semibold text-[11px] sm:text-sm flex items-center gap-1.5">
                                <i class="fas fa-eye"></i> <span class="hidden sm:inline">Voir les détails</span><span class="sm:hidden">Voir</span>
                            </span>
                            <span class="text-slate-500 text-[10px] sm:text-xs">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $item->created_at->diffForHumans(['locale' => 'fr']) }}
                            </span>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-3xl text-slate-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-50 mb-2">Aucun résultat trouvé</h3>
                    <p class="text-slate-400 mb-6 max-w-md mx-auto">Essayez d'ajuster vos critères de recherche ou de réinitialiser les filtres</p>
                    <a href="{{ route('all-items') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40">
                        <i class="fas fa-sync-alt"></i>
                        Réinitialiser les filtres
                    </a>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($items->hasPages())
            <div class="px-6 py-4 border-t border-slate-700 bg-slate-900">
                {{ $items->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Style de la pagination pour le thème sombre */
    .pagination {
        display: flex;
        gap: 4px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        background: #1E293B;
        border: 1px solid #334155;
        color: #94A3B8;
        transition: all 0.2s;
        text-decoration: none;
    }
    .pagination .page-item .page-link:hover {
        background: #334155;
        color: #F1F5F9;
        border-color: #3B82F6;
    }
    .pagination .page-item.active .page-link {
        background: #3B82F6;
        border-color: #3B82F6;
        color: #FFFFFF;
    }
    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    /* Line clamp */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@endsection