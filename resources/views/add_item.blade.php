@extends('layout')
@section("content")

@php
    $isLost = ($type ?? 'lost') === 'lost';
    $defaultStatus = $isLost ? 'lost' : 'found';

    $typeConfig = [
        'lost' => [
            'titleMain' => 'Déclarer un objet perdu',
            'titleSub' => 'Remplissez les informations ci-dessous pour signaler votre objet',
            'sectionTitle' => "Détails de l'objet perdu",
            'icon' => 'fa-box-open',
            'iconBg' => 'bg-blue-500/15',
            'iconColor' => 'text-blue-500',
            'dateLabel' => 'Date de perte *',
            'descPlaceholder' => "Décrivez l'objet en détail (couleur, marque, particularités...)",
            'descHint' => "Soyez précis pour augmenter vos chances de retrouver l'objet",
            'focusBorder' => 'focus:border-blue-500 focus:shadow-[0_0_0_3px_rgba(59,130,246,.12)]',
            'uploadZoneHover' => 'hover:border-blue-500',
            'uploadIconHover' => 'group-hover:text-blue-500',
            'buttonBg' => 'bg-blue-500 hover:bg-blue-600 shadow-blue-500/25 hover:shadow-blue-500/40',
        ],
        'found' => [
            'titleMain' => 'Ajouter un objet trouvé',
            'titleSub' => 'Vous avez trouvé un objet ? Aidez son propriétaire à le retrouver',
            'sectionTitle' => "Détails de l'objet trouvé",
            'icon' => 'fa-hand-holding-heart',
            'iconBg' => 'bg-emerald-500/15',
            'iconColor' => 'text-emerald-500',
            'dateLabel' => 'Date de trouvaille *',
            'descPlaceholder' => "Décrivez l'objet en détail (couleur, marque, particularités, lieu où vous l'avez trouvé...)",
            'descHint' => "Plus vous donnez de détails, plus le propriétaire pourra identifier son objet",
            'focusBorder' => 'focus:border-emerald-500 focus:shadow-[0_0_0_3px_rgba(16,185,129,.12)]',
            'uploadZoneHover' => 'hover:border-emerald-500',
            'uploadIconHover' => 'group-hover:text-emerald-500',
            'buttonBg' => 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/25 hover:shadow-emerald-500/40',
        ],
    ];

    $current = $typeConfig[$defaultStatus];
@endphp

<div class="min-h-[calc(100vh-64px)] bg-slate-900 py-8 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,.35)]">
            <div class="bg-slate-800 border-b border-slate-700 py-6 px-8">
                <div class="flex items-center gap-3">
                    <div id="header-icon-wrap" class="w-12 h-12 {{ $current['iconBg'] }} rounded-full flex items-center justify-center transition-colors">
                        <i id="header-icon" class="fas {{ $current['icon'] }} {{ $current['iconColor'] }} text-xl transition-colors"></i>
                    </div>
                    <div>
                        <h2 id="title-main" class="text-2xl font-bold text-slate-50">{{ $current['titleMain'] }}</h2>
                        <p id="title-sub" class="text-slate-400 text-sm mt-1">{{ $current['titleSub'] }}</p>
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

            <div class="p-8">
                <form action="{{ URL::to('save-item') }}" method="post" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <h3 class="text-lg font-semibold text-slate-50 mb-6 pb-3 border-b border-slate-700 flex items-center gap-2">
                            <i id="section-icon" class="fas fa-info-circle {{ $current['iconColor'] }} transition-colors"></i>
                            <span id="section-title">{{ $current['sectionTitle'] }}</span>
                        </h3>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-1.5">
                            <label for="item_name" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Nom de l'objet *</label>
                            <div class="relative">
                                <input type="text" id="item_name" name="item_name" value="{{ old('item_name') }}"
                                       class="js-focus-field w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all {{ $current['focusBorder'] }} placeholder:text-slate-500"
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
                                        class="js-focus-field w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all {{ $current['focusBorder'] }} appearance-none cursor-pointer">
                                    <option value="" disabled {{ old('category') ? '' : 'selected' }} class="bg-slate-800">Sélectionner une catégorie</option>
                                    @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->category_name }}" {{ old('category') == $categorie->category_name ? 'selected' : '' }} class="bg-slate-800">{{ $categorie->category_name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-500 text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-1.5">
                            <label for="lost_date" id="date-label" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">{{ $current['dateLabel'] }}</label>
                            <div class="relative">
                                <input type="date" id="lost_date" name="lost_date" value="{{ old('lost_date') }}"
                                       class="js-focus-field w-full bg-slate-900 border border-slate-700 rounded-lg py-3 pl-4 pr-10 text-sm text-slate-50 outline-none transition-all {{ $current['focusBorder'] }} [color-scheme:dark]" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-calendar-alt text-slate-500 text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="images" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Photo de l'objet *</label>
                            <label for="images" id="upload-zone" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-600 rounded-lg cursor-pointer bg-slate-900/50 hover:bg-slate-900 transition-all group {{ $current['uploadZoneHover'] }}">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i id="upload-icon" class="fas fa-cloud-upload-alt text-2xl text-slate-500 mb-3 transition-colors {{ $current['uploadIconHover'] }}"></i>
                                    <p class="text-sm text-slate-400">Cliquez pour uploader une photo</p>
                                    <p class="text-xs text-slate-500 mt-1">JPG, PNG ou WebP (max 2 Mo)</p>
                                </div>
                                <input type="file" id="images" name="images[]" class="hidden" accept="image/*" required>
                            </label>
                            <p id="file-name" class="text-xs text-slate-500 mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3">
                        <label class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Type d'annonce *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 bg-slate-900 p-4 rounded-lg border cursor-pointer transition-all hover:bg-slate-900/70 {{ old('status', $defaultStatus) == 'found' ? 'border-emerald-500 bg-emerald-500/5' : 'border-slate-700' }}"
                                   id="label-found">
                                <input type="radio" name="status" value="found" {{ old('status', $defaultStatus) == 'found' ? 'checked' : '' }}
                                       class="w-5 h-5 text-emerald-500 bg-slate-900 border-slate-600 focus:ring-emerald-500 focus:ring-offset-0 cursor-pointer">
                                <div>
                                    <span class="text-slate-50 font-medium block">Objet trouvé</span>
                                    <span class="text-slate-500 text-xs">J'ai trouvé cet objet</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 bg-slate-900 p-4 rounded-lg border cursor-pointer transition-all hover:bg-slate-900/70 {{ old('status', $defaultStatus) == 'lost' ? 'border-blue-500 bg-blue-500/5' : 'border-slate-700' }}"
                                   id="label-lost">
                                <input type="radio" name="status" value="lost" {{ old('status', $defaultStatus) == 'lost' ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-500 bg-slate-900 border-slate-600 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                                <div>
                                    <span class="text-slate-50 font-medium block">Objet perdu</span>
                                    <span class="text-slate-500 text-xs">J'ai perdu cet objet</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="description" class="text-xs font-semibold text-slate-400 uppercase tracking-[0.5px]">Description *</label>
                        <textarea id="description" name="description" rows="4"
                                  class="js-focus-field w-full bg-slate-900 border border-slate-700 rounded-lg py-3 px-4 text-sm text-slate-50 outline-none transition-all {{ $current['focusBorder'] }} placeholder:text-slate-500 resize-y"
                                  placeholder="{{ $current['descPlaceholder'] }}" required>{{ old('description') }}</textarea>
                        <p id="desc-hint" class="text-xs text-slate-500 mt-1">{{ $current['descHint'] }}</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" id="submit-btn"
                                class="w-full py-3 {{ $current['buttonBg'] }} text-white font-semibold rounded-lg transition-all shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Publier l'annonce
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const TYPE_CONFIG = @json($typeConfig);

    const FOCUS_LOST = TYPE_CONFIG.lost.focusBorder.split(' ');
    const FOCUS_FOUND = TYPE_CONFIG.found.focusBorder.split(' ');
    const UPLOAD_ZONE_LOST = TYPE_CONFIG.lost.uploadZoneHover.split(' ');
    const UPLOAD_ZONE_FOUND = TYPE_CONFIG.found.uploadZoneHover.split(' ');
    const UPLOAD_ICON_LOST = TYPE_CONFIG.lost.uploadIconHover.split(' ');
    const UPLOAD_ICON_FOUND = TYPE_CONFIG.found.uploadIconHover.split(' ');
    const BTN_LOST = TYPE_CONFIG.lost.buttonBg.split(' ');
    const BTN_FOUND = TYPE_CONFIG.found.buttonBg.split(' ');

    const ICON_BG = { lost: 'bg-blue-500/15', found: 'bg-emerald-500/15' };
    const ICON_COLOR = { lost: 'text-blue-500', found: 'text-emerald-500' };
    const ICON_CLASS = { lost: 'fa-box-open', found: 'fa-hand-holding-heart' };

    function swapClasses(el, remove, add) {
        if (!el) return;
        remove.forEach(c => el.classList.remove(c));
        add.forEach(c => el.classList.add(c));
    }

    function updateRadioStyles(type) {
        const labelFound = document.getElementById('label-found');
        const labelLost = document.getElementById('label-lost');

        if (type === 'found') {
            labelFound.classList.add('border-emerald-500', 'bg-emerald-500/5');
            labelFound.classList.remove('border-slate-700');
            labelLost.classList.remove('border-blue-500', 'bg-blue-500/5');
            labelLost.classList.add('border-slate-700');
        } else {
            labelLost.classList.add('border-blue-500', 'bg-blue-500/5');
            labelLost.classList.remove('border-slate-700');
            labelFound.classList.remove('border-emerald-500', 'bg-emerald-500/5');
            labelFound.classList.add('border-slate-700');
        }
    }

    function switchAnnouncementType(type) {
        const cfg = TYPE_CONFIG[type];
        if (!cfg) return;

        document.getElementById('title-main').textContent = cfg.titleMain;
        document.getElementById('title-sub').textContent = cfg.titleSub;
        document.getElementById('section-title').textContent = cfg.sectionTitle;
        document.getElementById('date-label').textContent = cfg.dateLabel;
        document.getElementById('desc-hint').textContent = cfg.descHint;

        const description = document.getElementById('description');
        description.placeholder = cfg.descPlaceholder;

        const headerIconWrap = document.getElementById('header-icon-wrap');
        const headerIcon = document.getElementById('header-icon');
        const sectionIcon = document.getElementById('section-icon');

        swapClasses(headerIconWrap, Object.values(ICON_BG), [ICON_BG[type]]);
        swapClasses(headerIcon, [...Object.values(ICON_COLOR), ...Object.values(ICON_CLASS)], [ICON_COLOR[type], ICON_CLASS[type], 'fas', 'text-xl', 'transition-colors']);
        swapClasses(sectionIcon, Object.values(ICON_COLOR), [ICON_COLOR[type], 'fas', 'fa-info-circle', 'transition-colors']);

        document.querySelectorAll('.js-focus-field').forEach(el => {
            swapClasses(el, [...FOCUS_LOST, ...FOCUS_FOUND], type === 'lost' ? FOCUS_LOST : FOCUS_FOUND);
        });

        const uploadZone = document.getElementById('upload-zone');
        const uploadIcon = document.getElementById('upload-icon');
        swapClasses(uploadZone, [...UPLOAD_ZONE_LOST, ...UPLOAD_ZONE_FOUND], type === 'lost' ? UPLOAD_ZONE_LOST : UPLOAD_ZONE_FOUND);
        swapClasses(uploadIcon, [...UPLOAD_ICON_LOST, ...UPLOAD_ICON_FOUND], type === 'lost' ? UPLOAD_ICON_LOST : UPLOAD_ICON_FOUND);

        const submitBtn = document.getElementById('submit-btn');
        swapClasses(submitBtn, [...BTN_LOST, ...BTN_FOUND], type === 'lost' ? BTN_LOST : BTN_FOUND);

        updateRadioStyles(type);
    }

    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                switchAnnouncementType(this.value);
            }
        });
    });

    document.getElementById('images').addEventListener('change', function (e) {
        const fileName = e.target.files[0]?.name;
        const fileNameDisplay = document.getElementById('file-name');
        if (fileName) {
            fileNameDisplay.textContent = '📎 ' + fileName;
            fileNameDisplay.classList.remove('hidden');
        } else {
            fileNameDisplay.classList.add('hidden');
        }
    });
})();
</script>

@endsection
