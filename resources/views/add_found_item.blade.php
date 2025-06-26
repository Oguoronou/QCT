@extends('layout')
@section("content")

<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Carte principale -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- En-tête avec icône -->
            <div class="bg-blue-600 py-5 px-6">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h2 class="text-2xl font-bold text-white">Ajouter un objet trouvé</h2>
                </div>
            </div>

            <!-- Corps du formulaire -->
            <div class="p-6 md:p-8">
                <form action="{{ URL::to('save-item') }}" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="found">

                    <!-- Section titre -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-6 pb-2 border-b border-gray-200">Détails de l'objet</h3>
                    </div>

                    <!-- Nom et Catégorie -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Nom de l'objet *</label>
                            <input type="text" name="item_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Portefeuille noir" required>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Catégorie *</label>
                            <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach ($categories as $key=>$categorie)
                                <option value="{{ $categorie->category_name }}">{{ $categorie->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Date et Image -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Date de trouvaille *</label>
                            <div class="relative">
                                <input type="date" name="lost_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Photo de l'objet *</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-500">Cliquez pour uploader</p>
                                    </div>
                                    <input type="file" name="image" class="hidden" accept="image/*" required>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Type d'annonce -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Type d'annonce *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 bg-gray-100 p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-200 transition">
                                <input type="radio" name="type" value="found" class="h-5 w-5 text-blue-600" checked>
                                <span class="text-gray-700 font-medium">Objet trouvé</span>
                            </label>
                            <label class="flex items-center space-x-3 bg-gray-100 p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-200 transition">
                                <input type="radio" name="type" value="lost" class="h-5 w-5 text-blue-600">
                                <span class="text-gray-700 font-medium">Objet perdu</span>
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Description *</label>
                        <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Décrivez l'objet en détail (couleur, marque, particularités...)" required></textarea>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md transition duration-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Publier l'annonce
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection