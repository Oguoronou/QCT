@extends('layout')
@section("content")

<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md"> <!-- Conteneur plus étroit et centré -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- En-tête -->
            <div class="bg-blue-600 py-4 px-6">
                <h2 class="text-xl font-bold text-white">S'inscrire</h2>
                
                @if(Session::has("message"))
                <div class="mt-2 bg-blue-500 text-white text-sm p-2 rounded">
                    {{ Session::get("message") }}
                </div>
                @endif
            </div>
            
            <!-- Corps du formulaire -->
            <div class="p-6">
                <form action="{{ URL::to('register') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Nom et Téléphone -->
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Nom complet</label>
                        <input type="text" name="name" class="w-full px-3 py-2 border rounded" placeholder="Votre nom" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Numéro mobile</label>
                        <input type="text" name="mobile_no" class="w-full px-3 py-2 border rounded" placeholder="0701234567" required>
                    </div>
                    
                    <!-- Email et Mot de passe -->
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border rounded" placeholder="email@exemple.com" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Mot de passe</label>
                        <input type="password" name="password" class="w-full px-3 py-2 border rounded" placeholder="••••••••" required>
                    </div>
                    
                    <!-- Ville et Pays -->
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Ville</label>
                        <input type="text" name="city" class="w-full px-3 py-2 border rounded" placeholder="Votre ville" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Pays</label>
                        <select name="country" class="w-full px-3 py-2 border rounded" required>
                            <option value="CI">Côte d'Ivoire</option>
                            <option value="BF">Burkina Faso</option>
                        </select>
                    </div>
                    
                    <!-- Adresse et Photo -->
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-1">Adresse</label>
                        <input type="text" name="address" class="w-full px-3 py-2 border rounded" placeholder="Votre adresse" required>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 mb-1">Photo de profil</label>
                        <input type="file" name="image" class="w-full px-3 py-2 border rounded" required>
                    </div>
                    
                    <!-- Bouton de soumission -->
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
                        S'inscrire
                    </button>
                </form>
                
                <!-- Lien de connexion -->
                <div class="mt-4 text-center">
                    <a href="{{ url('login') }}" class="text-blue-600 hover:underline">Déjà un compte? Connectez-vous</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection