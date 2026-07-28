@extends('layout')
@section('content')

<div class="min-h-[calc(100vh-64px)] bg-slate-900 py-8 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Bouton Retour -->
        <div class="mb-6">
            <a href="{{ url('my-items') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-blue-400 transition-colors text-sm">
                <i class="fas fa-arrow-left"></i>
                Retour à mes objets
            </a>
        </div>

        <!-- Carte principale -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <!-- En-tête -->
            <div class="bg-slate-800 border-b border-slate-700 py-6 px-8">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-500/15 rounded-full flex items-center justify-center">
                        <i class="fas fa-edit text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-slate-50">Modifier l'objet perdu</h2>
                        <p class="text-slate-400 text-sm mt-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/15 text-red-400 border border-red-500/25">
                                <i class="fas fa-search text-[10px]"></i> Objet perdu
                            </span>
                            &nbsp; #{{ $item->id }}
                        </p>
                    </div>
                </div>
                
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

            <!-- Corps du formulaire -->
            <div class="p-8">
                <form action="{{ URL::to('update-item') }}" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <input type="hidden" name="type" value="lost">

                    <!-- Section détails -->
                    <div>
                        <h3 class="text-lg font-semibold text-slate-50 mb-6 pb-3 border-b border-slate-700 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Détails de l'objet
                        </h3>
                    </div>
                    
                    <!-- Nom et Catégorie -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-1.5">
                            <label for="item_name" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Nom de l'objet *</label>
                            <div class="relative">
                                <input type="text" id="item_name" name="item_name" 
                                       value="{{ old('item_name', $item->item_name) }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500"
                                       placeholder="Ex: Portefeuille noir" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-tag text-slate-500 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1.5">
                            <label for="category" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Catégorie *</label>
                            <div class="relative">
                                <select id="category" name="category" required
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] appearance-none cursor-pointer">
                                    <option value="" disabled class="bg-slate-800">Sélectionnez une catégorie</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->category_name }}" {{ old('category', $item->category_name) == $category->category_name ? 'selected' : '' }} class="bg-slate-800">
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-500 text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date et Images -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-1.5">
                            <label for="lost_date" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Date de perte *</label>
                            <div class="relative">
                                <input type="date" id="lost_date" name="lost_date" 
                                       value="{{ old('lost_date', $item->date) }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] [color-scheme:dark]" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-calendar-alt text-slate-500 text-sm"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Nouvelles images</label>
                            <label for="newImages" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-600 rounded-lg cursor-pointer bg-slate-900/50 hover:bg-slate-900 hover:border-blue-500 transition-all group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-slate-500 group-hover:text-blue-500 mb-3 transition-colors"></i>
                                    <p class="text-sm text-slate-400">
                                        <span class="font-semibold">Cliquez pour uploader</span>
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">PNG, JPG, WebP (max 2 Mo)</p>
                                </div>
                                <input type="file" id="newImages" name="images[]" class="hidden" accept="image/*" multiple>
                            </label>
                            <p class="text-xs text-slate-500 mt-1">Laissez vide pour conserver les images actuelles</p>
                            <p id="newFilesList" class="text-xs text-blue-400 mt-1 hidden"></p>
                        </div>
                    </div>

                    <!-- Aperçu des images actuelles -->
                    @php
                        $images = $item->images ? explode(',', $item->images) : [];
                    @endphp
                    @if(count($images) > 0)
                    <div>
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px] block mb-3">Images actuelles ({{ count($images) }})</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($images as $image)
                            <div class="relative group">
                                <img src="{{ asset($image) }}" 
                                     class="w-24 h-24 object-cover rounded-xl border-2 border-slate-700 group-hover:border-blue-500 transition-colors"
                                     alt="Image {{ $loop->iteration }}">
                                <span class="absolute -top-2 -right-2 bg-blue-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-lg">
                                    {{ $loop->iteration }}
                                </span>
                                <div class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer"
                                     onclick="openImage('{{ asset($image) }}')">
                                    <i class="fas fa-search-plus text-white text-lg"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($item->policeDeclaration)
                    @php
                        $editCommissariats = \App\Models\Commissariat::where('is_active', true)->orWhere('id', $item->policeDeclaration->commissariat_id)->orderBy('commune')->get();
                    @endphp
                    <div>
                        <h3 class="text-lg font-semibold text-slate-50 mb-6 pb-3 border-b border-slate-700 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-blue-500"></i>
                            Déclaration au commissariat
                        </h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="flex flex-col gap-1.5">
                                <label for="commissariat_id" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Commissariat *</label>
                                <select id="commissariat_id" name="commissariat_id" required
                                        class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none transition-all focus:border-blue-500">
                                    @foreach($editCommissariats as $commissariat)
                                        <option value="{{ $commissariat->id }}" {{ old('commissariat_id', $item->policeDeclaration->commissariat_id) == $commissariat->id ? 'selected' : '' }}>
                                            {{ $commissariat->name }} ({{ $commissariat->commune }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label for="declaration_number" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">N° de déclaration *</label>
                                <input type="text" id="declaration_number" name="declaration_number" required
                                       value="{{ old('declaration_number', $item->policeDeclaration->declaration_number) }}"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none transition-all focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5 mt-6">
                            <label for="receipt_photo" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Photo du récépissé (optionnel)</label>
                            <input type="file" id="receipt_photo" name="receipt_photo" accept="image/*"
                                   class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white hover:file:bg-blue-600">
                            @if($item->policeDeclaration->receipt_photo)
                                <p class="text-xs text-slate-500 mt-1">Laissez vide pour conserver le récépissé actuel</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Description -->
                    <div class="flex flex-col gap-1.5">
                        <label for="description" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Description *</label>
                        <textarea id="description" name="description" rows="5"
                                  class="w-full bg-slate-900 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none transition-all focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)] placeholder:text-slate-500 resize-y"
                                  placeholder="Décrivez l'objet en détail (couleur, marque, particularités...)" required>{{ old('description', $item->description) }}</textarea>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" 
                                class="flex-1 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Mettre à jour l'objet
                        </button>
                        <a href="{{ url('my-items') }}" 
                           class="py-3 px-6 bg-slate-700 hover:bg-slate-600 text-slate-300 font-medium rounded-lg transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour afficher l'image en plein écran -->
<div id="imageModal" class="fixed inset-0 z-[300] bg-black/90 hidden items-center justify-center p-4 cursor-pointer" onclick="closeImage()">
    <button class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl transition-colors">
        <i class="fas fa-times"></i>
    </button>
    <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-xl object-contain" onclick="event.stopPropagation()" alt="Image en plein écran">
</div>

<script>
    // Afficher les noms des nouveaux fichiers sélectionnés
    document.getElementById('newImages').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const filesList = document.getElementById('newFilesList');
        
        if (files.length > 0) {
            filesList.textContent = '📎 ' + files.map(f => f.name).join(', ');
            filesList.classList.remove('hidden');
        } else {
            filesList.classList.add('hidden');
        }
    });

    // Modal image
    function openImage(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeImage() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeImage();
    });
</script>

@endsection