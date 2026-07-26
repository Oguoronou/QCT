@extends('layout')
@section("content")

<div class="min-h-[calc(100vh-64px)] bg-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 no-underline">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-lg text-white">
                    <i class="fas fa-search-location"></i>
                </div>
                <span class="text-2xl font-extrabold text-white tracking-[-0.5px]">Q<span class="text-blue-500">CT</span></span>
            </a>
        </div>

        <!-- Carte d'inscription -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête -->
            <div class="bg-slate-800 border-b border-slate-700 py-6 px-8 text-center">
                <div class="w-14 h-14 bg-blue-500/15 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-blue-500 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-50">Rejoignez la communauté</h2>
                <p class="text-slate-400 text-sm mt-2">Créez votre compte pour commencer</p>
                
                @if(Session::has("message"))
                <div class="mt-4 bg-emerald-500/15 border border-emerald-500/25 text-emerald-300 text-sm p-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ Session::get("message") }}
                </div>
                @endif

                @if($errors->any())
                <div class="mt-4 bg-red-500/15 border border-red-500/25 text-red-300 text-sm p-3 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            <!-- Formulaire -->
            <div class="p-8">
                <form action="{{ URL::to('register') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <!-- Nom complet -->
                    <div class="flex flex-col gap-1.5">
                        <label for="name" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Nom complet</label>
                        <div class="relative">
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="Votre nom complet" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-user text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Téléphone et Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label for="mobile_no" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Téléphone</label>
                            <div class="relative">
                                <input type="text" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                       placeholder="0701234567" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-mobile-alt text-slate-500 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Email</label>
                            <div class="relative">
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                       placeholder="email@exemple.com" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-envelope text-slate-500 text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mot de passe -->
                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Mot de passe</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="••••••••" required>
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-300 transition-colors">
                                <i id="toggleIcon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmation du mot de passe -->
                    <div class="flex flex-col gap-1.5">
                        <label for="password_confirmation" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Confirmer le mot de passe</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="••••••••" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-lock text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ville et Pays -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label for="city" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Ville</label>
                            <div class="relative">
                                <input type="text" id="city" name="city" value="{{ old('city') }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                       placeholder="Abidjan" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-city text-slate-500 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1.5">
                            <label for="country" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Pays</label>
                            <div class="relative">
                                <select id="country" name="country" required
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] appearance-none cursor-pointer">
                                    <option value="CI" class="bg-slate-800">Côte d'Ivoire</option>
                                    <option value="BF" class="bg-slate-800">Burkina Faso</option>
                                    <option value="SN" class="bg-slate-800">Sénégal</option>
                                    <option value="ML" class="bg-slate-800">Mali</option>
                                    <option value="GN" class="bg-slate-800">Guinée</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-500 text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Adresse -->
                    <div class="flex flex-col gap-1.5">
                        <label for="address" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Adresse</label>
                        <div class="relative">
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="Votre adresse complète" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-map-marker-alt text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Photo de profil -->
                    <div class="flex flex-col gap-1.5">
                        <label for="image" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Photo de profil</label>
                        <div class="relative">
                            <input type="file" id="image" name="image" accept="image/*" required
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-500/15 file:text-blue-400 hover:file:bg-blue-500/25 file:cursor-pointer file:transition-colors cursor-pointer outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)]">
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Format accepté : JPG, PNG. Taille max : 2 Mo</p>
                    </div>
                    
                    <!-- Conditions d'utilisation -->
                    <div class="flex items-start gap-2">
                        <input type="checkbox" id="terms" name="terms" required
                               class="mt-1 w-4 h-4 rounded border-slate-600 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                        <label for="terms" class="text-sm text-slate-400 cursor-pointer">
                            J'accepte les <a href="#" class="text-blue-400 hover:text-blue-300 hover:underline">conditions d'utilisation</a> et la <a href="#" class="text-blue-400 hover:text-blue-300 hover:underline">politique de confidentialité</a>
                        </label>
                    </div>
                    
                    <!-- Bouton d'inscription -->
                    <div>
                        <button type="submit" 
                                class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                            <i class="fas fa-user-plus"></i>
                            Créer mon compte
                        </button>
                    </div>
                    
                    <!-- Lien de connexion -->
                    <div class="text-center pt-4 border-t border-slate-700">
                        <p class="text-slate-400 text-sm">
                            Déjà un compte ? 
                            <a href="{{ url('login') }}" class="text-blue-400 font-medium hover:text-blue-300 hover:underline transition-colors">
                                Se connecter
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liens rapides -->
        <div class="text-center mt-6">
            <a href="{{ url('/') }}" class="text-sm text-slate-500 hover:text-slate-400 transition-colors inline-flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

@endsection