@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Paramètres du site</h1>
        <p class="text-gray-600 mt-1">Logo, coordonnées et réseaux sociaux affichés sur le site public</p>
    </div>

    @if(Session::has('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
        <p>{{ Session::get('message') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <section class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ url('admin/settings') }}" method="post" enctype="multipart/form-data">
                @csrf

                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-image mr-2 text-blue-600"></i>Logo &amp; identité</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du site <span class="text-red-500">*</span></label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        @if(!empty($settings['site_logo']))
                            <img src="{{ asset($settings['site_logo']) }}" alt="Logo actuel" class="h-12 mb-2 rounded">
                        @endif
                        <input type="file" name="site_logo" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description / slogan</label>
                        <textarea name="site_description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-address-book mr-2 text-blue-600"></i>Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <input type="text" name="contact_address" value="{{ old('contact_address', $settings['contact_address'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-share-alt mr-2 text-blue-600"></i>Réseaux sociaux</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-facebook-f mr-1"></i> Facebook</label>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                               placeholder="https://facebook.com/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-twitter mr-1"></i> Twitter / X</label>
                        <input type="url" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                               placeholder="https://x.com/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-instagram mr-1"></i> Instagram</label>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                               placeholder="https://instagram.com/..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-whatsapp mr-1"></i> WhatsApp</label>
                        <input type="url" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? '') }}"
                               placeholder="https://wa.me/2250700000000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center">
                        <i class="fas fa-save mr-2"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

@endsection
