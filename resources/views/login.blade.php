@extends('layout')
@section("content")

<div class="min-h-[calc(100vh-64px)] bg-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 no-underline">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-lg text-white">
                    <i class="fas fa-search-location"></i>
                </div>
                <span class="text-2xl font-extrabold text-white tracking-[-0.5px]">Q<span class="text-blue-500">CT</span></span>
            </a>
        </div>

        <!-- Carte de connexion -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête -->
            <div class="bg-slate-800 border-b border-slate-700 py-6 px-8 text-center">
                <div class="w-14 h-14 bg-blue-500/15 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-in-alt text-blue-500 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-50">Content de vous revoir</h2>
                <p class="text-slate-400 text-sm mt-2">Connectez-vous pour accéder à votre compte</p>
                
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
                <form action="{{ URL::to('login') }}" method="post" class="space-y-5">
                    @csrf
                    
                    <!-- Champ Email -->
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Email</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="votre@email.com" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-envelope text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Champ Mot de passe -->
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
                        <div class="mt-1.5 text-right">
                            <a href="{{ url('/forgot-password') }}" class="text-xs text-blue-400 hover:text-blue-300 hover:underline transition-colors">Mot de passe oublié ?</a>
                        </div>
                    </div>

                    <!-- Se souvenir de moi -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" 
                               class="w-4 h-4 rounded border-slate-600 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                        <label for="remember" class="ml-2 text-sm text-slate-400 cursor-pointer">Se souvenir de moi</label>
                    </div>
                    
                    <!-- Bouton de connexion -->
                    <div>
                        <button type="submit" 
                                class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            Se connecter
                        </button>
                    </div>
                    
                    <!-- Lien vers l'inscription -->
                    <div class="text-center pt-4 border-t border-slate-700">
                        <p class="text-slate-400 text-sm">
                            Pas encore de compte ? 
                            <a href="{{ url('register') }}" class="text-blue-400 font-medium hover:text-blue-300 hover:underline transition-colors">
                                Créer un compte
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