@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ajouter une question</h1>
    </div>

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ url('admin/save-faq') }}" method="post">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question <span class="text-red-500">*</span></label>
                    <input type="text" name="question" value="{{ old('question') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Réponse <span class="text-red-500">*</span></label>
                    <textarea name="answer" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>{{ old('answer') }}</textarea>
                </div>

                <div class="mb-6 max-w-[160px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ url('admin/faq') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@endsection
