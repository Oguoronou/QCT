@extends('layout')
@section('content')

<div class="min-h-[calc(100vh-64px)] bg-slate-900 py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Bouton Retour -->
        <div class="mb-6">
            <a href="{{ url('my-items') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-blue-400 transition-colors text-sm">
                <i class="fas fa-arrow-left"></i>
                Retour à mes objets
            </a>
        </div>

        <!-- Carte principale -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête -->
            <div class="bg-slate-800 border-b border-slate-700 py-6 px-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-500/15 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-50">Détail de l'objet</h2>
                        <p class="text-slate-400 text-sm mt-1">Informations complètes sur l'annonce</p>
                    </div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Colonne 1 - Détails de l'objet -->
                    <div>
                        <h3 class="text-lg font-semibold text-slate-50 mb-6 pb-3 border-b border-slate-700 flex items-center gap-2">
                            <i class="fas fa-box text-blue-500"></i>
                            Détails de l'objet
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">ID</span>
                                <span class="text-sm text-slate-50 font-mono">#{{ $item->id }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Créé le</span>
                                <span class="text-sm text-slate-50">{{ $item->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Nom</span>
                                <span class="text-sm text-slate-50 font-semibold">{{ $item->item_name }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Catégorie</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/15 text-blue-400 border border-blue-500/25">
                                    <i class="fas fa-{{ $item->category == 'documents' ? 'file-alt' : ($item->category == 'electronics' ? 'mobile-alt' : ($item->category == 'keys' ? 'key' : ($item->category == 'wallet' ? 'wallet' : 'tag'))) }} text-[10px]"></i>
                                    {{ $item->category_name }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Type</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $item->status == 'lost' ? 'bg-red-500/15 text-red-400 border-red-500/25' : 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25' }} border">
                                    <i class="fas fa-{{ $item->status == 'lost' ? 'search' : 'check' }} text-[10px]"></i>
                                    {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Statut</span>
                                @php
                                    $statusConfig = [
                                        'pending' => ['text' => 'En attente', 'color' => 'bg-amber-500/15 text-amber-400 border-amber-500/25', 'icon' => 'clock'],
                                        'claimed' => ['text' => 'Réclamation en cours', 'color' => 'bg-blue-500/15 text-blue-400 border-blue-500/25', 'icon' => 'hands-helping'],
                                        'ownership_claimed' => ['text' => 'Propriété revendiquée', 'color' => 'bg-purple-500/15 text-purple-400 border-purple-500/25', 'icon' => 'user-check'],
                                        'delivered' => ['text' => 'Rendu au propriétaire', 'color' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25', 'icon' => 'check-circle'],
                                        'returned' => ['text' => 'Restitué', 'color' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25', 'icon' => 'check-circle'],
                                        'found' => ['text' => 'Trouvé', 'color' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25', 'icon' => 'check-circle'],
                                    ];
                                    $config = $statusConfig[$item->lost_found_status] ?? ['text' => $item->lost_found_status, 'color' => 'bg-slate-500/15 text-slate-400 border-slate-500/25', 'icon' => 'circle'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $config['color'] }} border">
                                    <i class="fas fa-{{ $config['icon'] }} text-[10px]"></i>
                                    {{ $config['text'] }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Date</span>
                                <span class="text-sm text-slate-50">{{ $item->date ?? $item->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne 2 - Détails de l'utilisateur -->
                    <div>
                        <h3 class="text-lg font-semibold text-slate-50 mb-6 pb-3 border-b border-slate-700 flex items-center gap-2">
                            <i class="fas fa-user text-blue-500"></i>
                            Détails de l'utilisateur
                        </h3>
                        
                        @if($item->user)
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 mb-6 p-4 bg-slate-900 rounded-xl border border-slate-700">
                                <div class="w-14 h-14 rounded-full bg-blue-500/15 border border-blue-500/25 flex items-center justify-center text-blue-500 font-bold text-lg shrink-0">
                                    {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-50">{{ $item->user->name }}</p>
                                    <p class="text-sm text-slate-400">{{ $item->user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Nom</span>
                                <span class="text-sm text-slate-50">{{ $item->user->name }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Email</span>
                                <span class="text-sm text-slate-50">{{ $item->user->email }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Contact</span>
                                <span class="text-sm text-slate-50">{{ $item->user->mobile_no ?? 'Non renseigné' }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Pays</span>
                                <span class="text-sm text-slate-50">{{ $item->user->country ?? 'Non renseigné' }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/50">
                                <span class="text-sm text-slate-400">Ville</span>
                                <span class="text-sm text-slate-50">{{ $item->user->city ?? 'Non renseigné' }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-slate-400">Adresse</span>
                                <span class="text-sm text-slate-50">{{ $item->user->address ?? 'Non renseigné' }}</span>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-8 text-slate-500">
                            <i class="fas fa-user-slash text-2xl mb-2"></i>
                            <p>Aucune information utilisateur disponible</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-slate-50 mb-4 pb-3 border-b border-slate-700 flex items-center gap-2">
                        <i class="fas fa-align-left text-blue-500"></i>
                        Description
                    </h3>
                    <div class="bg-slate-900 border border-slate-700 p-5 rounded-xl">
                        <p class="text-slate-300 leading-relaxed">{{ $item->description ?? 'Aucune description fournie' }}</p>
                    </div>
                </div>

                <!-- Images -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-slate-50 mb-4 pb-3 border-b border-slate-700 flex items-center gap-2">
                        <i class="fas fa-images text-blue-500"></i>
                        Images
                    </h3>
                    @php
                        $images = $item->images ? explode(',', $item->images) : [];
                    @endphp
                    @if(count($images) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($images as $image)
                                <div class="overflow-hidden rounded-xl border border-slate-700 group cursor-pointer" onclick="openImage('{{ asset($image) }}')">
                                    <img src="{{ asset($image) }}" 
                                         class="w-full h-48 object-cover transition-transform duration-400 group-hover:scale-105" 
                                         alt="Image de l'objet"
                                         loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-slate-900 rounded-xl border border-slate-700">
                            <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-image text-2xl text-slate-500"></i>
                            </div>
                            <p class="text-slate-400">Aucune image disponible</p>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-slate-50 mb-4 pb-3 border-b border-slate-700 flex items-center gap-2">
                        <i class="fas fa-bolt text-blue-500"></i>
                        Actions
                    </h3>
                    
                    <div class="space-y-4">
                        @if($item->status == 'found' && $item->lost_found_status == 'pending' && Auth::id() != $item->user_id)
                            <form action="{{ route('claim-ownership', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full sm:w-auto px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 flex items-center justify-center gap-2">
                                    <i class="fas fa-hand-holding-heart"></i>
                                    @if($item->category_name == 'Personnes')
                                        C'est mon proche
                                    @else
                                        Cet objet m'appartient
                                    @endif
                                </button>
                            </form>
                        @endif

                        @if($item->status == 'lost' && $item->lost_found_status == 'pending' && Auth::id() != $item->user_id)
                            <form action="{{ route('claim-item', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full sm:w-auto px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    @if($item->category_name == 'Personnes')
                                        J'ai retrouvé cette personne
                                    @else
                                        J'ai trouvé cet objet
                                    @endif
                                </button>
                            </form>
                        @endif

                        @if($item->lost_found_status == 'ownership_claimed' && Auth::id() == $item->user_id)
                            <form action="{{ route('validate-ownership', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full sm:w-auto px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-semibold transition-all shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 flex items-center justify-center gap-2">
                                    <i class="fas fa-thumbs-up"></i>
                                    @if($item->category_name == 'Personnes')
                                        Confirmer les retrouvailles
                                    @else
                                        Confirmer la restitution
                                    @endif
                                </button>
                            </form>
                        @endif

                        @if($item->lost_found_status == 'claimed' && Auth::id() == $item->user_id)
                            <form action="{{ route('validate-claim', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full sm:w-auto px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-semibold transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                                    <i class="fas fa-thumbs-up"></i>
                                    @if($item->category_name == 'Personnes')
                                        Confirmer les retrouvailles
                                    @else
                                        Confirmer la récupération
                                    @endif
                                </button>
                            </form>
                        @endif

                        <!-- Messages d'état -->
                        @if($item->lost_found_status == 'ownership_claimed')
                            <div class="bg-blue-500/10 border border-blue-500/25 text-blue-400 px-5 py-4 rounded-xl flex items-start gap-3">
                                <i class="fas fa-info-circle mt-0.5"></i>
                                <div>
                                    <p class="font-medium">En attente de confirmation</p>
                                    <p class="text-sm mt-1">
                                        @if($item->category_name == 'Personnes')
                                            Un proche a signalé avoir retrouvé la personne.
                                        @else
                                            Un propriétaire a signalé que cet objet lui appartient.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($item->lost_found_status == 'returned' || $item->lost_found_status == 'delivered')
                            <div class="bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 px-5 py-4 rounded-xl flex items-start gap-3">
                                <i class="fas fa-check-circle mt-0.5 text-emerald-400"></i>
                                <div>
                                    <p class="font-medium">Résolu avec succès</p>
                                    <p class="text-sm mt-1">
                                        @if($item->category_name == 'Personnes')
                                            La personne a été rendue à son proche.
                                        @else
                                            L'objet a été rendu à son propriétaire.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($item->lost_found_status == 'claimed')
                            <div class="bg-amber-500/10 border border-amber-500/25 text-amber-400 px-5 py-4 rounded-xl flex items-start gap-3">
                                <i class="fas fa-hourglass-half mt-0.5"></i>
                                <div>
                                    <p class="font-medium">Réclamation en attente</p>
                                    <p class="text-sm mt-1">
                                        @if($item->category_name == 'Personnes')
                                            Une personne a signalé avoir retrouvé votre proche. Veuillez confirmer.
                                        @else
                                            Une personne a signalé avoir trouvé votre objet. Veuillez confirmer.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if(($item->lost_found_status == 'claimed' || $item->lost_found_status == 'ownership_claimed' || $item->lost_found_status == 'returned' || $item->lost_found_status == 'delivered') && $item->found_user_id)
                            <div class="mt-6 p-5 bg-slate-900 border border-slate-700 rounded-xl">
                                <h4 class="font-semibold text-slate-50 mb-4 flex items-center gap-2">
                                    <i class="fas fa-user-check text-blue-500"></i>
                                    @if($item->status == 'found')
                                        @if($item->category_name == 'Personnes')
                                            Proche ayant reconnu la personne
                                        @else
                                            Propriétaire ayant reconnu l'objet
                                        @endif
                                    @else
                                        @if($item->category_name == 'Personnes')
                                            Personne ayant retrouvé
                                        @else
                                            Personne ayant trouvé
                                        @endif
                                    @endif
                                </h4>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-blue-500/15 border border-blue-500/25 flex items-center justify-center text-blue-500 font-bold shrink-0">
                                        {{ strtoupper(substr($item->foundUser->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-50">{{ $item->foundUser->name ?? 'Utilisateur inconnu' }}</p>
                                        <p class="text-sm text-slate-400">{{ $item->foundUser->email ?? '' }}</p>
                                        @if($item->foundUser->mobile_no ?? false)
                                            <p class="text-sm text-slate-400 mt-1">
                                                <i class="fas fa-phone mr-1"></i> {{ $item->foundUser->mobile_no }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher l'image en plein écran -->
<div id="imageModal" class="fixed inset-0 z-[300] bg-black/90 hidden items-center justify-center p-4 cursor-pointer" onclick="closeImage()">
    <button class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl transition-colors">
        <i class="fas fa-times"></i>
    </button>
    <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-xl object-contain" onclick="event.stopPropagation()" alt="Image en plein écran">
</div>

<script>
function openImage(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImage() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImage();
});
</script>

@endsection