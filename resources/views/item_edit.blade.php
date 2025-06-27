@extends('layout')
@section('content')

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Carte principale -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- En-tête -->
            <div class="bg-blue-600 py-5 px-6">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-white">Modifier l'objet perdu</h2>
                </div>
            </div>

            <!-- Corps du formulaire -->
            <div class="p-6 md:p-8">
                <form action="{{ URL::to('update-item') }}" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <input type="hidden" name="type" value="lost">

                    <h3 class="text-xl font-semibold text-gray-800 mb-6 pb-2 border-b border-gray-200">Détails de l'objet</h3>
                    
                    <!-- Nom et Catégorie -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Nom de l'objet *</label>
                            <input type="text" name="item_name" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Ex: Portefeuille noir" 
                                   value="{{ $item->item_name }}" required>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Catégorie *</label>
                            <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="" disabled selected>Sélectionnez une catégorie</option>

                                {{-- <option value="Mobile" {{ $item->category_name == 'Mobile' ? 'selected' : '' }}>Mobile</option> --}}
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_name }}" {{ $item->category_name == $category->category_name ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>

                                @endforeach
                                {{-- <option value="Vélo" {{ $item->category_name == 'Vélo' ? 'selected' : '' }}>Vélo</option>
                                <option value="Voiture" {{ $item->category_name == 'Voiture' ? 'selected' : '' }}>Voiture</option> --}}
                                <!-- Ajoutez d'autres options au besoin -->
                            </select>
                        </div>
                    </div>

                    <!-- Date et Image -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Date de perte *</label>
                            <input type="date" name="lost_date" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                value="{{ $item->date }}" required>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Images</label>
                            <div class="flex flex-wrap gap-4">
                                <!-- Aperçu des images actuelles -->
                                @if($item->images)
                                    @foreach(explode(',', $item->images) as $image)
                                    <div class="relative">
                                        <img src="{{ asset($image) }}" 
                                            class="w-16 h-16 object-cover rounded-lg border border-gray-300">
                                        <span class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                            {{ $loop->iteration }}
                                        </span>
                                    </div>
                                    @endforeach
                                @endif
                                
                                <!-- Champ de téléchargement -->
                                <div class="flex-1">
                                    <div class="flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500">
                                                    <span class="font-semibold">Cliquez pour uploader</span>
                                                </p>
                                                <p class="text-xs text-gray-500">PNG, JPG (MAX. 2MB)</p>
                                            </div>
                                            <input type="file" name="images[]" class="hidden" multiple>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour conserver les images actuelles</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Description *</label>
                        <textarea name="description" rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Décrivez l'objet en détail (couleur, marque, particularités...)" 
                                  required>{{ $item->description }}</textarea>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition duration-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Mettre à jour l'objet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection