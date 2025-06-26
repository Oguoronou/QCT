@extends('layout')
@section("content")

<div class="container py-5">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- En-tête -->
        <div class="bg-blue-600 py-4 px-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-box-open mr-2"></i> Mes Objets
                </h1>
                <a href="{{ url('add-item') }}" class="btn bg-white text-blue-600 hover:bg-blue-50 font-medium">
                    <i class="fas fa-plus mr-2"></i> Ajouter un objet
                </a>
            </div>
        </div>

        <!-- Contenu -->
        <div class="p-6">
            <!-- Filtres -->
            <div class="mb-6 flex flex-wrap items-center gap-4">
                <div class="relative">
                    <select id="statusFilter" class="pl-10 pr-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="lost">Perdus</option>
                        <option value="found">Trouvés</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-filter text-gray-400"></i>
                    </div>
                </div>
                
                <div class="relative">
                    <select id="stateFilter" class="pl-10 pr-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les états</option>
                        <option value="pending">En attente</option>
                        <option value="resolved">Résolus</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-info-circle text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Tableau responsive -->
            <div class="overflow-x-auto">
                <table id="itemsTable" class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">Objet</th>
                            <th class="px-6 py-3 text-left">Catégorie</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Photos</th>
                            <th class="px-6 py-3 text-left">Statut</th>
                            <th class="px-6 py-3 text-left">État</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($items as $item)
                        <tr class="hover:bg-gray-50">
                            <!-- Nom de l'objet -->
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $item->item_name }}
                            </td>
                            
                            <!-- Catégorie -->
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                    {{ $item->category_name }}
                                </span>
                            </td>
                            
                            <!-- Date -->
                            <td class="px-6 py-4 text-gray-500">
                                {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                            </td>
                            
                            <!-- Images -->
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    @php
                                        $images = explode(',', $item->images);
                                        $displayImages = array_slice($images, 0, 3);
                                    @endphp
                                    @foreach($displayImages as $image)
                                        <img class="h-10 w-10 rounded-full border-2 border-white object-cover" 
                                             src="{{ asset($image) }}" 
                                             alt="Photo objet">
                                    @endforeach
                                    @if(count($images) > 3)
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 border-2 border-white text-xs font-medium">
                                            +{{ count($images) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Statut -->
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $item->status == 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
                                </span>
                            </td>
                            
                            <!-- État -->
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $item->lost_found_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $item->lost_found_status == 'pending' ? 'En attente' : 'Résolu' }}
                                </span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <!-- Bouton Détails -->
                                    <a href="{{ url('item-detail', $item->id) }}" 
                                       class="p-2 text-blue-600 hover:text-blue-900 rounded-full hover:bg-blue-50"
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Bouton Modifier -->
                                    <a href="{{ url('edit-item', $item->id) }}" 
                                       class="p-2 text-green-600 hover:text-green-900 rounded-full hover:bg-green-50"
                                       title="Modifier">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    
                                    <!-- Bouton Supprimer -->
                                    <a href="{{ url('delete-item', $item->id) }}" 
                                       class="p-2 text-red-600 hover:text-red-900 rounded-full hover:bg-red-50"
                                       title="Supprimer"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objet ?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    
                                    <!-- Actions conditionnelles -->
                                    @if($item->status == "lost" && $item->lost_found_status == "pending")
                                    <a href="{{ url('item-found', $item->id) }}" 
                                       class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-full flex items-center"
                                       title="Marquer comme trouvé">
                                        <i class="fas fa-check mr-1"></i> Trouvé
                                    </a>
                                    @elseif($item->status == "found" && $item->lost_found_status == "pending")
                                    <a href="{{ url('item-deliver', $item->id) }}" 
                                       class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-full flex items-center"
                                       title="Marquer comme livré">
                                        <i class="fas fa-truck mr-1"></i> Livré
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Message si vide -->
            @if($items->isEmpty())
            <div class="text-center py-10">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Aucun objet enregistré</h3>
                <p class="text-gray-500 mt-1">Commencez par ajouter un objet perdu ou trouvé</p>
                <a href="{{ url('add-item') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-plus mr-2"></i> Ajouter un objet
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('#itemsTable').DataTable({
            responsive: true,
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 },
                { orderable: false, targets: -1 }
            ],
            initComplete: function() {
                // Appliquer les filtres
                $('#statusFilter, #stateFilter').on('change', function() {
                    var status = $('#statusFilter').val();
                    var state = $('#stateFilter').val();
                    
                    table.column(4).search(status).column(5).search(state).draw();
                });
            }
        });
    });
</script>

@endsection