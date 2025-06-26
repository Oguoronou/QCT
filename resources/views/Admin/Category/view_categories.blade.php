@extends('Admin.layout')
@section('content')

<main class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestion des Catégories</h1>
            <p class="text-gray-600 mt-1">Liste complète des catégories disponibles</p>
        </div>
        <a href="{{ url('admin/add-category') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center justify-center transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Nouvelle Catégorie
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
        <div>
            <p class="font-medium">Succès !</p>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-exclamation-circle text-red-500 mr-2 mt-0.5"></i>
        <div>
            <p class="font-medium">Erreur !</p>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Categories Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list-alt mr-2 text-blue-600"></i>
                Liste des Catégories
            </h3>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icône</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $category->category_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->icon)
                            <div class="flex items-center">
                                <i class="{{ $category->icon }} text-lg mr-2 text-blue-600"></i>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $category->icon }}</span>
                            </div>
                            @else
                            <span class="text-sm text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="{{ url('admin/edit-category/'.$category->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-full hover:bg-blue-50"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ url('admin/delete-category/'.$category->id) }}" method="POST" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-full hover:bg-red-50"
                                            title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-lg font-medium">Aucune catégorie trouvée</p>
                                <p class="text-sm mt-1">Commencez par ajouter une nouvelle catégorie</p>
                                <a href="{{ url('admin/add-category') }}" class="mt-4 text-blue-600 hover:text-blue-800 flex items-center">
                                    <i class="fas fa-plus mr-1"></i> Ajouter une catégorie
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (optional) -->
        {{-- @if($categories->links())
        <div class="px-5 py-3 border-t bg-gray-50">
            {{ $categories->links() }}
        </div>
        @endif --}}
    </div>
</main>

@endsection