@extends('layout')
@section("content")

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Carte principale -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- En-tête -->
            <div class="bg-blue-600 py-5 px-6">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-white">Mon compte</h2>
                </div>
                
                @if(Session::has("message"))
                <div class="mt-4 bg-green-500 text-white text-sm p-3 rounded-lg">
                    {{ Session::get("message") }}
                </div>
                @endif
            </div>
            
            <!-- Corps du formulaire -->
            <div class="p-6 md:p-8">
                <form action="{{ URL::to('update-profile') }}" method="post" class="space-y-6">
                    @csrf
                    
                    <!-- Nom complet -->
                    <div>
                        <label for="name" class="block text-gray-700 font-medium mb-2">Nom complet *</label>
                        <input type="text" id="name" name="name" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Votre nom complet" 
                               value="{{ Auth::user()->name ?? '' }}" required>
                    </div>
                    
                    <!-- Email (lecture seule) -->
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Adresse e-mail *</label>
                        <input type="email" id="email" name="email" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" 
                               placeholder="Votre email" 
                               value="{{ Auth::user()->email ?? '' }}" readonly>
                    </div>
                    
                    <!-- Rôle (lecture seule) -->
                    <div>
                        <label for="role" class="block text-gray-700 font-medium mb-2">Rôle *</label>
                        <input type="text" id="role" name="role" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" 
                               placeholder="Votre rôle" 
                               value="{{ Auth::user()->role ?? '' }}" readonly>
                    </div>
                    
                    <!-- Numéro de mobile -->
                    <div>
                        <label for="mobile_no" class="block text-gray-700 font-medium mb-2">Numéro de mobile *</label>
                        <input type="text" id="mobile_no" name="mobile_no" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Ex: 0701234567" 
                               value="{{ Auth::user()->mobile_no ?? '' }}" required>
                    </div>
                    
                    <!-- Pays et Ville -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="country" class="block text-gray-700 font-medium mb-2">Pays *</label>
                            <select id="country" name="country" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="" disabled selected>Sélectionnez votre pays</option>
                                <option value="CI" {{ Auth::user()->country == 'CI' ? 'selected' : '' }}>Côte d'Ivoire</option>
                                <option value="BF" {{ Auth::user()->country == 'BF' ? 'selected' : '' }}>Burkina Faso</option>
                                <option value="SN" {{ Auth::user()->country == 'SN' ? 'selected' : '' }}>Sénégal</option>
                                <!-- Ajoutez d'autres pays au besoin -->
                            </select>
                        </div>
                        
                        <div>
                            <label for="city" class="block text-gray-700 font-medium mb-2">Ville *</label>
                            <input type="text" id="city" name="city" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Votre ville" 
                                   value="{{ Auth::user()->city ?? '' }}" required>
                        </div>
                    </div>
                    
                    <!-- Adresse -->
                    <div>
                        <label for="address" class="block text-gray-700 font-medium mb-2">Adresse *</label>
                        <input type="text" id="address" name="address" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Votre adresse complète" 
                               value="{{ Auth::user()->address ?? '' }}" required>
                    </div>
                    
                    <!-- Bouton de soumission -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition duration-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Mettre à jour le profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection