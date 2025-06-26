@extends('Admin.layout')
@section('content')

<main class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ajouter une nouvelle catégorie</h1>
        <nav class="flex mt-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                <li class="inline-flex items-center">
                    <a href="{{ url('admin/dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <a href="{{ url('admin/categories') }}" class="text-sm text-gray-600 hover:text-blue-600">Catégories</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <span class="text-sm font-medium text-blue-600 ml-1">Nouvelle catégorie</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Form Card -->
    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-plus-circle mr-2 text-blue-600"></i>
                Formulaire de création
            </h3>
        </div>

        <div class="p-6">
            <form action="{{ URL::to('admin/save-category') }}" method="post">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Icon Field with Preview -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                            Icône FontAwesome <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="icon" name="icon" value="{{ old('icon') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="fas fa-icon-name" required
                                   oninput="document.getElementById('icon-preview').className = this.value || 'fas fa-icons'">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i id="icon-preview" class="{{ old('icon') ?? 'fas fa-icons' }} text-gray-400"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            <p>Exemples :</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <span class="px-2 py-1 bg-gray-100 rounded cursor-pointer hover:bg-blue-100" onclick="document.getElementById('icon').value = 'fas fa-box'">
                                    <i class="fas fa-box mr-1"></i>fa-box
                                </span>
                                <span class="px-2 py-1 bg-gray-100 rounded cursor-pointer hover:bg-blue-100" onclick="document.getElementById('icon').value = 'fas fa-tshirt'">
                                    <i class="fas fa-tshirt mr-1"></i>fa-tshirt
                                </span>
                                <span class="px-2 py-1 bg-gray-100 rounded cursor-pointer hover:bg-blue-100" onclick="document.getElementById('icon').value = 'fas fa-laptop'">
                                    <i class="fas fa-laptop mr-1"></i>fa-laptop
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Category Name Field -->
                    <div>
                        <label for="category_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom de la catégorie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="category_name" name="category_name" value="{{ old('category_name') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Ex: Électronique" required>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end mt-8 space-x-3">
                    <button type="reset" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center">
                        <i class="fas fa-undo mr-2"></i> Réinitialiser
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@endsection