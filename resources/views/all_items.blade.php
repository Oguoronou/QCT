@extends('layout')
@section("content")

<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Barre de recherche améliorée -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('all-items') }}" class="space-y-4 md:space-y-0 md:grid md:grid-cols-5 md:gap-4">
                <!-- Champ de recherche -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input name="search" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                           type="search" placeholder="Rechercher..." value="{{ request()->input('search') }}">
                </div>
                
                <!-- Sélecteur de catégorie -->
                <div>
                    <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_name }}" {{ request()->input('category') == $category->category_name ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sélecteur de statut -->
                <div>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous statuts</option>
                        <option value="lost" {{ request()->input('status') == 'lost' ? 'selected' : '' }}>Objets perdus</option>
                        <option value="found" {{ request()->input('status') == 'found' ? 'selected' : '' }}>Objets trouvés</option>
                    </select>
                </div>
                
                <!-- Boutons d'action -->
                <div class="grid grid-cols-2 gap-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('all-items') }}" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-4 rounded-lg font-medium transition flex items-center justify-center">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Résultats -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $items->count() }} résultat{{ $items->count() > 1 ? 's' : '' }} trouvé{{ $items->count() > 1 ? 's' : '' }}
                </h2>
                @if(request()->has('search') || request()->has('category') || request()->has('status'))
                <p class="text-gray-600 mt-1">
                    Filtres actifs: 
                    @if(request()->input('search')) <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded ml-1">{{ request()->input('search') }}</span> @endif
                    @if(request()->input('category')) <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded ml-1">{{ request()->input('category') }}</span> @endif
                    @if(request()->input('status')) <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded ml-1">{{ request()->input('status') == 'lost' ? 'Perdus' : 'Trouvés' }}</span> @endif
                </p>
                @endif
            </div>
            
            <!-- Liste des résultats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                @forelse ($items as $item)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-2 border border-gray-100">
                <div class="relative">
                    <img class="w-full h-64 object-cover" src="{{ asset(explode(',', $item->images)[0]) }}" alt="Objet perdu/trouvé">
                    <span class="absolute top-4 right-4 {{ $item->status == 'lost' ? 'bg-red-500' : 'bg-green-500' }} text-white text-xs px-3 py-1 rounded-full shadow">
                        {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-{{ $item->category == 'documents' ? 'file-alt' : ($item->category == 'electronics' ? 'plug' : 'box') }} text-blue-500"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-xl">{{ $item->item_name }}</h5>
                            <p class="text-sm text-gray-500">{{ ucfirst($item->category) }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">{{ Str::limit($item->description, 100) }}</p>
                    <div class="flex items-center justify-between">
                        <a href="{{ url('item-detail', $item->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-lg font-medium transition flex items-center">
                            <i class="fas fa-eye mr-2"></i> Voir
                        </a>
                        <span class="text-gray-400 text-sm">{{ $item->created_at->diffForHumans(['locale' => 'fr']) }}</span>
                    </div>
                </div>
            </div>
                @empty
                <div class="col-span-full py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun résultat trouvé</h3>
                    <p class="mt-1 text-gray-500">Essayez d'ajuster vos critères de recherche</p>
                    <a href="{{ route('all-items') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Réinitialiser les filtres
                    </a>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection