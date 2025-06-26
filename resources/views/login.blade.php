@extends('layout')
@section("content")

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- En-tête avec bordure colorée -->
            <div class="bg-blue-600 py-6 px-8 text-center">
                <h2 class="text-2xl font-bold text-white flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Connexion
                </h2>
                
                @if(Session::has("message"))
                <div class="mt-4 bg-blue-500/90 text-white text-sm p-3 rounded-lg">
                    {{ Session::get("message") }}
                </div>
                @endif
            </div>
            
            <!-- Corps du formulaire -->
            <div class="p-8">
                <form action="{{ URL::to('login') }}" method="post" class="space-y-6">
                    @csrf
                    
                    <!-- Champ Email -->
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="votre@email.com" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Champ Mot de passe -->
                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="••••••••" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">Mot de passe oublié ?</a>
                        </div>
                    </div>
                    
                    <!-- Bouton de connexion -->
                    <div>
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                            Se connecter
                        </button>
                    </div>
                    
                    <!-- Lien vers l'inscription -->
                    <div class="text-center pt-4">
                        <p class="text-gray-600">Pas encore de compte ? 
                            <a href="{{ url('register') }}" class="text-blue-600 font-medium hover:text-blue-800 hover:underline">S'inscrire</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection