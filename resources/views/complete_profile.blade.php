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

        <!-- Carte -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête -->
            <div class="bg-slate-800 border-b border-slate-700 py-6 px-8 text-center">
                <div class="w-14 h-14 bg-blue-500/15 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-edit text-blue-500 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-50">Complétez votre profil</h2>
                <p class="text-slate-400 text-sm mt-2">Encore quelques informations pour finaliser votre compte</p>

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
                <form action="{{ URL::to('complete-profile') }}" method="post" class="space-y-5">
                    @csrf

                    <!-- Téléphone -->
                    <div class="flex flex-col gap-1.5">
                        <label for="mobile_no" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Téléphone</label>
                        <div class="relative">
                            <input type="text" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="0700000000" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-phone text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pays -->
                    <div class="flex flex-col gap-1.5">
                        <label for="country" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Pays</label>
                        <div class="relative">
                            <input type="text" id="country" name="country" value="{{ old('country', 'Côte d\'Ivoire') }}"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                   placeholder="Côte d'Ivoire" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-globe-africa text-slate-500 text-sm"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ville -->
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

                    <!-- Bouton -->
                    <div>
                        <button type="submit"
                                class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            Continuer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
