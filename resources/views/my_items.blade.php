@extends('layout')
@section("content")

<div class="min-h-[calc(100vh-64px)] bg-slate-900 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Carte principale -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête -->
            <div class="bg-slate-800 border-b border-slate-700 py-6 px-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-500/15 rounded-full flex items-center justify-center">
                            <i class="fas fa-box-open text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-50">Mes Objets</h1>
                            <p class="text-slate-400 text-sm mt-1">{{ $items->total() }} objet{{ $items->total() > 1 ? 's' : '' }} enregistré{{ $items->total() > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ url('add-item') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 text-sm">
                            <i class="fas fa-search"></i> Objet perdu
                        </a>
                        <a href="{{ url('add-found-item') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 text-sm">
                            <i class="fas fa-hand-holding-heart"></i> Objet trouvé
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-6">
                @if($items->isEmpty())
                <!-- Message si vide -->
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-3xl text-slate-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-50 mb-2">Aucun objet enregistré</h3>
                    <p class="text-slate-400 mb-6 max-w-md mx-auto">Commencez par ajouter un objet perdu ou trouvé pour aider la communauté</p>
                    <div class="flex justify-center gap-3">
                        <a href="{{ url('add-item') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40">
                            <i class="fas fa-plus"></i> Ajouter un objet perdu
                        </a>
                        <a href="{{ url('add-found-item') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40">
                            <i class="fas fa-plus"></i> Ajouter un objet trouvé
                        </a>
                    </div>
                </div>
                @else
                <!-- Filtres -->
                <div class="mb-6 flex flex-wrap items-center gap-4 p-4 bg-slate-900 rounded-xl border border-slate-700">
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <i class="fas fa-filter"></i>
                        <span>Filtrer :</span>
                    </div>
                    
                    <div class="relative">
                        <select id="statusFilter" onchange="applyFilters()"
                                class="pl-10 pr-4 py-2.5 bg-slate-800 border border-slate-600 rounded-lg text-sm text-slate-300 outline-none transition-all focus:border-blue-500 appearance-none cursor-pointer">
                            <option value="">Tous les statuts</option>
                            <option value="lost">Perdus</option>
                            <option value="found">Trouvés</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-tag text-slate-500 text-xs"></i>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <select id="stateFilter" onchange="applyFilters()"
                                class="pl-10 pr-4 py-2.5 bg-slate-800 border border-slate-600 rounded-lg text-sm text-slate-300 outline-none transition-all focus:border-blue-500 appearance-none cursor-pointer">
                            <option value="">Tous les états</option>
                            <option value="pending">En attente</option>
                            <option value="resolved">Résolus</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-info-circle text-slate-500 text-xs"></i>
                        </div>
                    </div>

                    <div class="relative flex-1 min-w-[200px]">
                        <input type="search" id="searchFilter" oninput="applyFilters()"
                               placeholder="Rechercher un objet..."
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-800 border border-slate-600 rounded-lg text-sm text-slate-300 outline-none transition-all focus:border-blue-500 placeholder:text-slate-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-500 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Tableau responsive -->
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap" id="itemsTable">
                        <thead>
                            <tr class="border-b border-slate-700">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Objet</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-[0.5px] hidden md:table-cell">Catégorie</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-[0.5px] hidden lg:table-cell">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-[0.5px] hidden lg:table-cell">Photos</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-[0.5px] hidden sm:table-cell">État</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @foreach($items as $item)
                            <tr class="item-row hover:bg-slate-700/50 transition-colors" 
                                data-status="{{ $item->status }}"
                                data-state="{{ $item->lost_found_status == 'pending' ? 'pending' : 'resolved' }}"
                                data-search="{{ strtolower($item->item_name . ' ' . $item->category_name . ' ' . $item->description) }}">
                                <!-- Nom de l'objet -->
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        @php $firstImage = explode(',', $item->images)[0] ?? null; @endphp
                                        @if($firstImage)
                                            <img src="{{ asset($firstImage) }}" class="w-10 h-10 rounded-lg object-cover border border-slate-600 hidden sm:block" alt="" onerror="imgFallback(this)">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-slate-700 border border-slate-600 flex items-center justify-center hidden sm:flex shrink-0">
                                                <i class="fas fa-image text-slate-500"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium text-slate-50 truncate max-w-[200px]">{{ $item->item_name }}</p>
                                            <p class="text-xs text-slate-400 md:hidden">{{ $item->category_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Catégorie -->
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        {{ $item->category_name }}
                                    </span>
                                </td>
                                
                                <!-- Date -->
                                <td class="px-4 py-4 text-sm text-slate-400 hidden lg:table-cell">
                                    {{ \Carbon\Carbon::parse($item->date ?? $item->created_at)->format('d/m/Y') }}
                                </td>
                                
                                <!-- Images -->
                                <td class="px-4 py-4 hidden lg:table-cell">
                                    @php
                                        $images = explode(',', $item->images);
                                        $displayImages = array_slice($images, 0, 3);
                                    @endphp
                                    <div class="flex -space-x-2">
                                        @foreach($displayImages as $image)
                                            <img class="h-8 w-8 rounded-full border-2 border-slate-800 object-cover"
                                                 src="{{ asset($image) }}"
                                                 alt="Photo"
                                                 onerror="imgFallback(this)">
                                        @endforeach
                                        @if(count($images) > 3)
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-600 border-2 border-slate-800 text-xs font-medium text-slate-300">
                                                +{{ count($images) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Statut -->
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium border
                                        {{ $item->status == 'lost' ? 'bg-red-500/10 text-red-400 border-red-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' }}">
                                        <i class="fas fa-{{ $item->status == 'lost' ? 'search' : 'check' }} text-[10px]"></i>
                                        {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
                                    </span>
                                </td>
                                
                                <!-- État -->
                                <td class="px-4 py-4 hidden sm:table-cell">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium border
                                        {{ $item->lost_found_status == 'pending' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-purple-500/10 text-purple-400 border-purple-500/20' }}">
                                        <i class="fas fa-{{ $item->lost_found_status == 'pending' ? 'clock' : 'check-circle' }} text-[10px]"></i>
                                        {{ $item->lost_found_status == 'pending' ? 'En attente' : 'Résolu' }}
                                    </span>
                                </td>
                                
                                <!-- Actions -->
                                <td class="px-4 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <!-- Bouton Détails -->
                                        <a href="{{ url('item-detail', $item->id) }}" 
                                           class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 rounded-lg transition-colors"
                                           title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Bouton Modifier -->
                                        <a href="{{ url('edit-item', $item->id) }}" 
                                           class="p-2 text-slate-400 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-lg transition-colors"
                                           title="Modifier">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        
                                        <!-- Bouton Supprimer -->
                                        <form action="{{ url('delete-item', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet objet ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors"
                                                    title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($items->hasPages())
                <div class="mt-6 px-4">
                    {{ $items->links() }}
                </div>
                @endif
                @endif
            </div>
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
    
    /* Ligne filtrée cachée */
    .item-row.hidden-row {
        display: none;
    }
</style>

<script>
    // Filtrage côté client
    function applyFilters() {
        const statusFilter = document.getElementById('statusFilter').value;
        const stateFilter = document.getElementById('stateFilter').value;
        const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
        
        document.querySelectorAll('.item-row').forEach(row => {
            let show = true;
            
            if (statusFilter && row.dataset.status !== statusFilter) {
                show = false;
            }
            
            if (stateFilter && row.dataset.state !== stateFilter) {
                show = false;
            }
            
            if (searchFilter && !row.dataset.search.includes(searchFilter)) {
                show = false;
            }
            
            row.classList.toggle('hidden-row', !show);
        });
    }

    // Réinitialiser les filtres au chargement
    document.addEventListener('DOMContentLoaded', function() {
        applyFilters();
    });
</script>

@endsection