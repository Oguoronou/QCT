@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier : {{ $page->title }}</h1>
        <p class="text-gray-600 mt-1">Slug : {{ $page->slug }}</p>
    </div>

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ url('admin/pages/'.$page->slug) }}" method="post">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenu</label>
                    <textarea id="content" name="content" rows="15">{{ old('content', $page->content) }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ url('admin/pages') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

<script src="{{ asset('customerdesign/assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 450,
        menubar: false,
        plugins: 'lists link table code',
        toolbar: 'undo redo | formatselect | bold italic | bullist numlist | link table | code',
    });
</script>

@endsection
