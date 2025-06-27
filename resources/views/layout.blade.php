<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCT - Plateforme d'objets perdus et trouvés</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')
    
    <style>
        :root {
            --primary: #4154f1;
            --primary-hover: #717ff5;
            --dark: #0a0e34;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        .success-carousel {
        scroll-behavior: smooth;
    }
    
    .carousel-item {
        transition: transform 0.5s ease, opacity 0.5s ease;
    }
    
    .carousel-item:hover {
        transform: translateY(-5px);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .success-carousel .carousel-item {
        animation: fadeIn 0.6s ease forwards;
    }
    
    .success-carousel .carousel-item:nth-child(1) { animation-delay: 0.1s; }
    .success-carousel .carousel-item:nth-child(2) { animation-delay: 0.2s; }
    .success-carousel .carousel-item:nth-child(3) { animation-delay: 0.3s; }
    .success-carousel .carousel-item:nth-child(4) { animation-delay: 0.4s; }
    .success-carousel .carousel-item:nth-child(5) { animation-delay: 0.5s; }
        .footer {
            background: linear-gradient(135deg, var(--dark-color), #1a237e);
            color: white;
            padding: 60px 0 30px;
            margin-top: auto;
        }   
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="text-2xl font-bold text-primary">
                    QCT
                </a>
                
                <!-- Mobile menu button -->
                <button class="md:hidden rounded-md p-2 text-gray-700 hover:bg-gray-100 focus:outline-none" 
                        @click="open = !open">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <div class="flex space-x-4">
                        <a href="{{ url('/') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->is('/') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                            Accueil
                        </a>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 flex items-center">
                                <i class="fas fa-user-circle mr-1"></i> Mon compte
                                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <a href="{{ url('my-items') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-box mr-2"></i> Mes objets
                                    </a>
                                    <a href="{{ url('all-items') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-search mr-2"></i> Tous les objets
                                    </a>
                                    <a href="{{ url('add-item') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-plus-circle mr-2"></i> Ajouter un objet
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="{{ url('my-account') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Paramètres
                                    </a>
                                    <a href="{{ url('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <a href="#" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-envelope mr-1"></i> Contact
                        </a>
                    </div>
                    
                    <div class="ml-4">
                        <a href="{{ url('add-item') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-hover">
                            <i class="fas fa-plus mr-2"></i> Déclarer un objet
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="md:hidden" x-show="open" @click.away="open = false">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->is('/') ? 'bg-blue-50 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                    Accueil
                </a>
                
                <div class="relative">
                    <button class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100 flex items-center justify-between">
                        <span><i class="fas fa-user-circle mr-1"></i> Mon compte</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div class="pl-4">
                        <a href="{{ url('my-items') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-box mr-2"></i> Mes objets
                        </a>
                        <a href="{{ url('all-items') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-search mr-2"></i> Tous les objets
                        </a>
                        <a href="{{ url('add-item') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-plus-circle mr-2"></i> Ajouter un objet
                        </a>
                        <a href="{{ url('my-account') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i> Paramètres
                        </a>
                        <a href="{{ url('logout') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                        </a>
                    </div>
                </div>
                
                <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-envelope mr-1"></i> Contact
                </a>
                
                <a href="{{ url('add-item') }}" class="block w-full text-center px-3 py-2 rounded-md text-base font-medium text-white bg-primary hover:bg-primary-hover">
                    <i class="fas fa-plus mr-1"></i> Déclarer un objet
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-dark to-blue-900 text-white mt-auto">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Colonne 1 - Logo et description -->
                <div>
                    <div class="text-3xl font-bold mb-4">
                        <span class="text-primary">Q</span>CT
                    </div>
                    <p class="text-gray-300 mb-4">
                        Plateforme collaborative pour retrouver les objets perdus et rendre les objets trouvés à leurs propriétaires.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-blue-800 flex items-center justify-center hover:bg-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Colonne 2 - Liens rapides -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 relative pb-2 after:absolute after:left-0 after:bottom-0 after:w-10 after:h-0.5 after:bg-primary">
                        Liens rapides
                    </h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/') }}" class="text-gray-300 hover:text-white transition">Accueil</a></li>
                        <li><a href="{{ url('all-items') }}" class="text-gray-300 hover:text-white transition">Objets perdus/trouvés</a></li>
                        <li><a href="{{ url('add-item') }}" class="text-gray-300 hover:text-white transition">Déclarer un objet</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Comment ça marche ?</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- Colonne 3 - Catégories -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 relative pb-2 after:absolute after:left-0 after:bottom-0 after:w-10 after:h-0.5 after:bg-primary">
                        Catégories
                    </h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Portefeuilles</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Téléphones</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Clés</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Documents</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition">Animaux</a></li>
                    </ul>
                </div>
                
                <!-- Colonne 4 - Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 relative pb-2 after:absolute after:left-0 after:bottom-0 after:w-10 after:h-0.5 after:bg-primary">
                        Contact
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-300"></i>
                            <span class="text-gray-300">Abidjan, Côte d'Ivoire</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone mt-1 mr-2 text-gray-300"></i>
                            <span class="text-gray-300">+225 XX XX XX XX</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-2 text-gray-300"></i>
                            <span class="text-gray-300">contact@qct.ci</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
                &copy; {{ date('Y') }} QCT. Tous droits réservés.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
    @yield('scripts')
</body>
</html>