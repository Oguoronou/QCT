@extends('Admin.layout')
@section('content')

<main class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profil Administrateur</h1>
        <nav class="flex mt-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                <li class="inline-flex items-center">
                    <a href="{{ url('admin/dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                        <span class="text-sm font-medium text-blue-600 ml-1">Profil</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Card Header with Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'overview'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'overview', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview'}"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Aperçu
                </button>
                <button @click="activeTab = 'edit'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'edit', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'edit'}"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-user-edit mr-2"></i>
                    Modifier le profil
                </button>
                <button @click="activeTab = 'password'" 
                        :class="{'border-blue-500 text-blue-600': activeTab === 'password', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'password'}"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-key mr-2"></i>
                    Changer le mot de passe
                </button>
            </nav>
        </div>

        <!-- Flash Message -->
        @if (Session::has('message'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mx-6 mt-4 rounded flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-2 mt-0.5"></i>
            <p>{{ Session::get('message') }}</p>
        </div>
        @endif

        <!-- Tab Content -->
        <div class="p-6" x-data="{ activeTab: 'overview' }">
            <!-- Overview Tab -->
            <div x-show="activeTab === 'overview'" class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                            <i class="fas fa-user text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ Auth::user()->name ?? '' }}</h3>
                        <p class="text-sm text-gray-500">Membre depuis {{ Auth::user()->created_at->format('d/m/Y') ?? '' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Détails du profil</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nom complet</label>
                                <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->name ?? '' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="mt-1 text-sm text-gray-900 flex items-center">
                                    {{ Auth::user()->email ?? '' }}
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">vérifié</span>
                                </p>
                            </div>
                        </div>
                        
                        <!-- You can add more fields here when needed -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Dernière connexion</label>
                                <p class="mt-1 text-sm text-gray-900">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Rôle</label>
                                <p class="mt-1 text-sm text-gray-900">Administrateur</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Tab -->
            <div x-show="activeTab === 'edit'" class="space-y-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Modifier le profil</h4>
                
                <form action="{{ URL::to('admin/update-profile') }}" method="post">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                            <input type="text" name="name" id="name" value="{{ Auth::user()->name ?? '' }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ Auth::user()->email ?? '' }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" 
                                   readonly disabled>
                            <p class="mt-1 text-xs text-gray-500">Contactez l'administrateur pour changer votre email</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-save mr-2"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Tab -->
            <div x-show="activeTab === 'password'" class="space-y-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Changer le mot de passe</h4>
                
                <form action="{{ url('admin/update-password') }}" method="post">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                            <div class="relative">
                                <input type="password" name="password" id="currentPassword" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" 
                                        onclick="togglePasswordVisibility('currentPassword', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                            <div class="relative">
                                <input type="password" name="newpassword" id="newPassword" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" 
                                        onclick="togglePasswordVisibility('newPassword', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label for="renewPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                            <div class="relative">
                                <input type="password" name="renewpassword" id="renewPassword" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-10">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" 
                                        onclick="togglePasswordVisibility('renewPassword', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center">
                            <i class="fas fa-key mr-2"></i> Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    // Toggle password visibility
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>

@endsection