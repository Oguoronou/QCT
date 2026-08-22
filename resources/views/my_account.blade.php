@extends('layout')
@section("content")

<div class="min-h-[calc(100vh-64px)] bg-slate-900 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Carte principale -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête avec avatar -->
            <div class="bg-slate-800 border-b border-slate-700 py-8 px-8 text-center">
                <div class="relative inline-block mb-4">
                    @if (Auth::user()->image)
                        <img src="{{ asset(Auth::user()->image) }}" alt="{{ Auth::user()->name }}"
                             class="w-24 h-24 rounded-full object-cover border-4 border-blue-500/30 mx-auto"
                             onerror="imgFallback(this)">
                    @else
                        <div class="w-24 h-24 rounded-full bg-blue-500/15 border-4 border-blue-500/30 flex items-center justify-center mx-auto">
                            <span class="text-4xl font-bold text-blue-400">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-emerald-500 border-4 border-slate-800 flex items-center justify-center">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-slate-50">{{ Auth::user()->name }}</h2>
                <p class="text-slate-400 text-sm mt-1">{{ Auth::user()->email }}</p>
                <span class="inline-flex items-center gap-1.5 mt-3 px-3 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                    <i class="fas fa-shield-alt"></i>
                    {{ ucfirst(Auth::user()->role ?? 'Utilisateur') }}
                </span>
            </div>

            <!-- Corps du formulaire -->
            <div class="p-8">
                <form action="{{ URL::to('update-profile') }}" method="post" class="space-y-6">
                    @csrf
                    
                    <div class="pb-4 border-b border-slate-700">
                        <h3 class="text-lg font-semibold text-slate-50 flex items-center gap-2">
                            <i class="fas fa-user-edit text-blue-500"></i>
                            Informations personnelles
                        </h3>
                    </div>
                    
                    <!-- Nom complet -->
                    <div class="flex flex-col gap-1.5">
                        <label for="name" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Nom complet *</label>
                        <div class="relative">
                            <input type="text" id="name" name="name" 
                                   value="{{ old('name', Auth::user()->name) }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="Votre nom complet" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-user text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email (lecture seule) -->
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Adresse e-mail *</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" 
                                   value="{{ Auth::user()->email }}"
                                   class="w-full bg-slate-900/50 border border-slate-600 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-400 outline-none cursor-not-allowed"
                                   placeholder="Votre email" readonly>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-lock text-slate-500 text-sm"></i>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">L'adresse email ne peut pas être modifiée</p>
                    </div>
                    
                    <!-- Rôle (lecture seule) -->
                    <div class="flex flex-col gap-1.5">
                        <label for="role" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Rôle</label>
                        <div class="relative">
                            <input type="text" id="role" name="role" 
                                   value="{{ ucfirst(Auth::user()->role ?? 'Utilisateur') }}"
                                   class="w-full bg-slate-900/50 border border-slate-600 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-400 outline-none cursor-not-allowed"
                                   placeholder="Votre rôle" readonly>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-shield-alt text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <div class="pb-4 border-b border-slate-700 pt-2">
                        <h3 class="text-lg font-semibold text-slate-50 flex items-center gap-2">
                            <i class="fas fa-address-book text-blue-500"></i>
                            Coordonnées
                        </h3>
                    </div>
                    
                    <!-- Numéro de mobile -->
                    <div class="flex flex-col gap-1.5">
                        <label for="mobile_no" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Numéro de mobile *</label>
                        <div class="relative">
                            <input type="text" id="mobile_no" name="mobile_no" 
                                   value="{{ old('mobile_no', Auth::user()->mobile_no) }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="Ex: 0701234567" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-mobile-alt text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pays et Ville -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-1.5">
                            <label for="country" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Pays *</label>
                            <div class="relative">
                                <select id="country" name="country" required
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] appearance-none cursor-pointer">
                                    <option value="" disabled {{ Auth::user()->country ? '' : 'selected' }} class="bg-slate-800">Sélectionnez votre pays</option>
                                    <option value="CI" {{ old('country', Auth::user()->country) == 'CI' ? 'selected' : '' }} class="bg-slate-800">🇨🇮 Côte d'Ivoire</option>
                                    <option value="BF" {{ old('country', Auth::user()->country) == 'BF' ? 'selected' : '' }} class="bg-slate-800">🇧🇫 Burkina Faso</option>
                                    <option value="SN" {{ old('country', Auth::user()->country) == 'SN' ? 'selected' : '' }} class="bg-slate-800">🇸🇳 Sénégal</option>
                                    <option value="ML" {{ old('country', Auth::user()->country) == 'ML' ? 'selected' : '' }} class="bg-slate-800">🇲🇱 Mali</option>
                                    <option value="GN" {{ old('country', Auth::user()->country) == 'GN' ? 'selected' : '' }} class="bg-slate-800">🇬🇳 Guinée</option>
                                    <option value="TG" {{ old('country', Auth::user()->country) == 'TG' ? 'selected' : '' }} class="bg-slate-800">🇹🇬 Togo</option>
                                    <option value="BJ" {{ old('country', Auth::user()->country) == 'BJ' ? 'selected' : '' }} class="bg-slate-800">🇧🇯 Bénin</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-500 text-xs"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1.5">
                            <label for="city" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Ville *</label>
                            <div class="relative">
                                <input type="text" id="city" name="city" 
                                       value="{{ old('city', Auth::user()->city) }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                       placeholder="Votre ville" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-city text-slate-500 text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Adresse -->
                    <div class="flex flex-col gap-1.5">
                        <label for="address" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Adresse *</label>
                        <div class="relative">
                            <input type="text" id="address" name="address" 
                                   value="{{ old('address', Auth::user()->address) }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="Votre adresse complète" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-map-marker-alt text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations supplémentaires -->
                    <div class="grid md:grid-cols-2 gap-6 p-4 bg-slate-900 rounded-xl border border-slate-700">
                        <div class="text-center">
                            <p class="text-xs text-slate-400 uppercase tracking-[0.5px] mb-1">Membre depuis</p>
                            <p class="text-sm font-semibold text-slate-50">{{ Auth::user()->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-slate-400 uppercase tracking-[0.5px] mb-1">Dernière mise à jour</p>
                            <p class="text-sm font-semibold text-slate-50">{{ Auth::user()->updated_at->diffForHumans(['locale' => 'fr']) }}</p>
                        </div>
                    </div>
                    
                    <!-- Bouton de soumission -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Mettre à jour le profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection