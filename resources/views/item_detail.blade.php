@extends('layout')
@section('content')

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Bouton Retour -->
        <div class="mb-6">
            <a href="{{ url('my-items') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Retour à mes objets
            </a>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- En-tête -->
            <div class="bg-blue-600 py-5 px-6">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h2 class="text-2xl font-bold text-white">Détail de l'objet</h2>
                </div>
            </div>

            <!-- Onglets -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <a href="#" class="border-b-2 border-blue-500 text-blue-600 px-4 py-4 text-sm font-medium">Aperçu</a>
                </nav>
            </div>

            <!-- Contenu -->
            <div class="p-6 md:p-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Colonne 1 - Détails de l'objet -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Détails de l'objet</h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">ID:</div>
                                <div class="col-span-3">{{ $item->id ?? "12312" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Créé le:</div>
                                <div class="col-span-3">{{ $item->created_at ?? "1h" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Nom:</div>
                                <div class="col-span-3 font-medium">{{ $item->item_name ?? "12312" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Catégorie:</div>
                                <div class="col-span-3">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                        {{ $item->category_name ?? "12312" }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Type:</div>
                                <div class="col-span-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->status == 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->status ?? "pending" }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Statut:</div>
                                <div class="col-span-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->lost_found_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $item->lost_found_status ?? "pending" }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Date:</div>
                                <div class="col-span-3">{{ $item->date ?? "buy" }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne 2 - Détails de l'utilisateur -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Détails de l'utilisateur</h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Nom:</div>
                                <div class="col-span-3">{{ $item->users->name ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Email:</div>
                                <div class="col-span-3">{{ $item->users->email ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Contact:</div>
                                <div class="col-span-3">{{ $item->users->mobile_no ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Pays:</div>
                                <div class="col-span-3">{{ $item->users->country ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Ville:</div>
                                <div class="col-span-3">{{ $item->users->city ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-1 font-medium text-gray-700">Adresse:</div>
                                <div class="col-span-3">{{ $item->users->address ?? "" }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Description</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $item->description }}</p>
                    </div>
                </div>

                <!-- Images -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Images</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @php
                            $images = explode(',', $item->images);
                        @endphp
                        @foreach($images as $image)
                            <div class="overflow-hidden rounded-lg border border-gray-200">
                                <img src="{{ asset($image) }}" 
                                     class="w-full h-48 object-cover hover:scale-105 transition duration-300" 
                                     alt="Image de l'objet">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection