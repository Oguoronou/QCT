@extends('layout')
@section("content")

<!-- Hero Section - Version améliorée -->
<div class="relative w-full h-screen max-h-[700px] overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('3.png') }}" alt="Hero" class="object-cover w-full h-full opacity-70">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-blue-600/80"></div>
    </div>
    <div class="relative z-10 container mx-auto px-6 h-full flex items-center">
        <div class="max-w-2xl text-center mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight drop-shadow-xl">
                Retrouvez ce qui compte vraiment
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-lg mx-auto">
                QCT connecte les objets perdus avec leurs propriétaires et aide à retrouver les personnes disparues.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('add-item') }}" 
                   class="px-8 py-4 bg-white text-blue-700 font-bold rounded-xl shadow-lg hover:bg-blue-100 transition transform hover:-translate-y-1">
                   <i class="fas fa-search mr-2"></i> J'ai perdu quelque chose
                </a>
                <a href="{{ url('add-found-item') }}" 
                   class="px-8 py-4 bg-green-500 text-white font-bold rounded-xl shadow-lg hover:bg-green-600 transition transform hover:-translate-y-1">
                   <i class="fas fa-hands-helping mr-2"></i> J'ai trouvé un objet
                </a>
            </div>
        </div>
    </div>
    <div class="absolute bottom-10 left-0 right-0 flex justify-center z-10">
        <a href="#how-it-works" class="animate-bounce">
            <div class="w-10 h-10 rounded-full bg-white/30 backdrop-blur-sm flex items-center justify-center">
                <i class="fas fa-chevron-down text-white"></i>
            </div>
        </a>
    </div>
</div>

<!-- How it works - Version améliorée -->
<section id="how-it-works" class="py-20 bg-gradient-to-b from-white to-blue-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                Fonctionnement
            </span>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                Comment <span class="text-blue-600">QCT</span> vous aide
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Notre plateforme utilise la puissance de la communauté pour réunir les objets perdus avec leurs propriétaires.
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-10">
            <!-- Step 1 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition border-t-4 border-blue-500">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold mb-6">
                    1
                </div>
                <h3 class="text-xl font-bold mb-3">Signalez la perte ou la trouvaille</h3>
                <p class="text-gray-600">
                    Créez une annonce détaillée avec photos et description pour augmenter les chances de retrouvailles.
                </p>
            </div>
            
            <!-- Step 2 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition border-t-4 border-green-500">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-2xl font-bold mb-6">
                    2
                </div>
                <h3 class="text-xl font-bold mb-3">Notre communauté agit</h3>
                <p class="text-gray-600">
                    Des milliers d'utilisateurs reçoivent des alertes et peuvent aider à identifier ou localiser ce que vous cherchez.
                </p>
            </div>
            
            <!-- Step 3 -->
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition border-t-4 border-purple-500">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-2xl font-bold mb-6">
                    3
                </div>
                <h3 class="text-xl font-bold mb-3">Retrouvailles</h3>
                <p class="text-gray-600">
                    Nous facilitons la mise en contact et la vérification pour des retrouvailles en toute sécurité.
                </p>
            </div>
        </div>
        
        <div class="mt-16 flex justify-center">
            <img src="{{asset('2.png')}}" alt="Processus QCT" class="rounded-2xl shadow-xl max-w-full md:max-w-2xl">
        </div>
    </div>
</section>

<!-- Personnes Disparues - Version améliorée -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-red-100 text-red-600 rounded-full text-sm font-semibold mb-4">
                Urgent
            </span>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                Personnes Disparues
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Votre aide peut sauver des vies. Si vous avez des informations, contactez-nous immédiatement.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($persons as $key=>$person)
                @if ($key < 6)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-2 border border-gray-100">
                    <div class="relative">
                        <img class="w-full h-64 object-cover" src="{{ asset(explode(',', $person->images)[0]) }}" alt="Personne disparue">
                        <span class="absolute top-4 right-4 bg-red-500 text-white text-xs px-3 py-1 rounded-full shadow">Disparu(e)</span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-500"></i>
                            </div>
                            <h5 class="font-bold text-xl">{{ $person->item_name }}</h5>
                        </div>
                        <p class="text-gray-600 mb-4">{{ Str::limit($person->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <a href="{{ url('item-detail', $person->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-lg font-medium transition flex items-center">
                                <i class="fas fa-info-circle mr-2"></i> Détails
                            </a>
                            <span class="text-gray-400 text-sm">{{ $person->created_at->diffForHumans(['locale' => 'fr']) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        
        <div class="flex justify-center mt-12">
            <a href="{{ url('/all-items?category=personne') }}" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-list mr-2"></i> Voir toutes les disparitions
            </a>
        </div>
    </div>
</section>

<!-- Objets Perdus et Retrouvés - Version améliorée -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                Récent
            </span>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                Objets Perdus et Retrouvés
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Parcourez les objets récemment signalés comme perdus ou trouvés.
            </p>
        </div>
        
        <!-- Navigation par catégories -->
        <div class="flex overflow-x-auto pb-4 mb-8 scrollbar-hide">
            <div class="flex space-x-3">
                <button class="px-5 py-2 bg-blue-600 text-white rounded-full font-medium whitespace-nowrap">
                    Tous
                </button>
                <button class="px-5 py-2 bg-white text-gray-700 rounded-full font-medium whitespace-nowrap shadow-sm hover:bg-gray-100">
                    <i class="fas fa-wallet mr-2"></i> Portefeuilles
                </button>
                <button class="px-5 py-2 bg-white text-gray-700 rounded-full font-medium whitespace-nowrap shadow-sm hover:bg-gray-100">
                    <i class="fas fa-mobile-alt mr-2"></i> Téléphones
                </button>
                <button class="px-5 py-2 bg-white text-gray-700 rounded-full font-medium whitespace-nowrap shadow-sm hover:bg-gray-100">
                    <i class="fas fa-key mr-2"></i> Clés
                </button>
                <button class="px-5 py-2 bg-white text-gray-700 rounded-full font-medium whitespace-nowrap shadow-sm hover:bg-gray-100">
                    <i class="fas fa-id-card mr-2"></i> Documents
                </button>
                <button class="px-5 py-2 bg-white text-gray-700 rounded-full font-medium whitespace-nowrap shadow-sm hover:bg-gray-100">
                    <i class="   mr-2"></i> Animaux
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($items as $key=>$item)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-2 border border-gray-100">
                <div class="relative">
                    <img class="w-full h-64 object-cover" src="{{ asset(explode(',', $item->images)[0]) }}" alt="Objet perdu/trouvé">
                    <span class="absolute top-4 right-4 {{ $item->status == 'lost' ? 'bg-red-500' : 'bg-green-500' }} text-white text-xs px-3 py-1 rounded-full shadow">
                        {{ $item->status == 'lost' ? 'Perdu' : 'Trouvé' }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-{{ $item->category == 'documents' ? 'file-alt' : ($item->category == 'electronics' ? 'plug' : 'box') }} text-blue-500"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-xl">{{ $item->item_name }}</h5>
                            <p class="text-sm text-gray-500">{{ ucfirst($item->category) }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">{{ Str::limit($item->description, 100) }}</p>
                    <div class="flex items-center justify-between">
                        <a href="{{ url('item-detail', $item->id) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-5 py-2 rounded-lg font-medium transition flex items-center">
                            <i class="fas fa-eye mr-2"></i> Voir
                        </a>
                        <span class="text-gray-400 text-sm">{{ $item->created_at->diffForHumans(['locale' => 'fr']) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="flex justify-center mt-12">
            <a href="{{ url('/all-items') }}" class="px-8 py-3 bg-white text-blue-600 rounded-xl font-bold shadow-lg hover:bg-gray-100 transition flex items-center border border-gray-200">
                <i class="fas fa-search mr-2"></i> Explorer tous les objets
            </a>
        </div>
    </div>
</section>

<!-- Statistiques -->
<section class="py-20 bg-blue-600 text-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4">Notre impact</h2>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                Depuis notre création, nous avons aidé à réunir des milliers de personnes avec leurs biens perdus.
            </p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8 text-center">
            <div class="bg-white/10 p-8 rounded-2xl backdrop-blur-sm">
                <div class="text-5xl font-bold mb-2">142K+</div>
                <p class="text-blue-100">Retrouvailles</p>
            </div>
            <div class="bg-white/10 p-8 rounded-2xl backdrop-blur-sm">
                <div class="text-5xl font-bold mb-2">75K+</div>
                <p class="text-blue-100">Objets rendus</p>
            </div>
            <div class="bg-white/10 p-8 rounded-2xl backdrop-blur-sm">
                <div class="text-5xl font-bold mb-2">1.2M+</div>
                <p class="text-blue-100">Membres</p>
            </div>
            <div class="bg-white/10 p-8 rounded-2xl backdrop-blur-sm">
                <div class="text-5xl font-bold mb-2">24/7</div>
                <p class="text-blue-100">Disponibilité</p>
            </div>
        </div>
    </div>
</section>

<!-- Témoignages -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-yellow-100 text-yellow-600 rounded-full text-sm font-semibold mb-4">
                Témoignages
            </span>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                Ce que disent nos utilisateurs
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Des histoires qui nous motivent à continuer notre mission.
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Témoignage 1 -->
            <div class="bg-gray-50 p-8 rounded-2xl border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl mr-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Jean Koffi</h4>
                        <p class="text-sm text-gray-500">Abidjan</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">
                    "Grâce à QCT, j'ai retrouvé mon portefeuille avec tous mes documents en moins de 48h. Une plateforme incroyable !"
                </p>
                <div class="flex mt-4 text-yellow-400">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <!-- Témoignage 2 -->
            <div class="bg-gray-50 p-8 rounded-2xl border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl mr-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Amina Traoré</h4>
                        <p class="text-sm text-gray-500">Bouaké</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">
                    "Mon téléphone perdu dans un taxi a été retrouvé grâce à la communauté QCT. Merci infiniment !"
                </p>
                <div class="flex mt-4 text-yellow-400">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
            </div>
            
            <!-- Témoignage 3 -->
            <div class="bg-gray-50 p-8 rounded-2xl border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl mr-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Marc Kouadio</h4>
                        <p class="text-sm text-gray-500">Yamoussoukro</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">
                    "J'ai pu retrouver mon chien perdu grâce aux alertes QCT. La rapidité de la communauté est impressionnante."
                </p>
                <div class="flex mt-4 text-yellow-400">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Donation Section - Version améliorée -->
<section class="py-20 bg-gradient-to-r from-blue-900 to-blue-700 text-white">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1 bg-white/20 rounded-full text-sm font-semibold mb-4">
                    Soutien
                </span>
                <h2 class="text-4xl font-bold mb-4">Soutenez notre initiative</h2>
                <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                    Votre don nous aide à maintenir et améliorer la plateforme pour aider plus de personnes.
                </p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 shadow-lg">
                <form id="cinetpay-form" action="javascript:void(0);" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="amount" class="block text-blue-100 mb-2">Montant du don (XOF)</label>
                            <div class="relative">
                                <input type="number" class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white placeholder-blue-200" 
                                       name="amount" id="amount" placeholder="Ex: 5000" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-blue-200">FCFA</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="customer_phone_number" class="block text-blue-100 mb-2">Téléphone</label>
                            <input type="text" class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white placeholder-blue-200" 
                                   name="customer_phone_number" id="customer_phone_number" placeholder="Ex: 0701234567" required>
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-blue-100 mb-2">Nom</label>
                            <input type="text" class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white placeholder-blue-200" 
                                   name="customer_name" id="customer_name" placeholder="Votre nom" required>
                        </div>
                        <div>
                            <label for="customer_surname" class="block text-blue-100 mb-2">Prénom</label>
                            <input type="text" class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white placeholder-blue-200" 
                                   name="customer_surname" id="customer_surname" placeholder="Votre prénom" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="customer_email" class="block text-blue-100 mb-2">Email</label>
                        <input type="email" class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white placeholder-blue-200" 
                               name="customer_email" id="customer_email" placeholder="Votre email" required>
                    </div>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label for="customer_address" class="block text-blue-100 mb-2">Adresse</label>
                            <input type="text" class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white placeholder-blue-200" 
                                   name="customer_address" id="customer_address" placeholder="Votre adresse" required>
                        </div>
                        <div>
                            <label for="customer_city" class="block text-blue-100 mb-2">Ville</label>
                            <input type="text" class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white placeholder-blue-200" 
                                   name="customer_city" id="customer_city" placeholder="Votre ville" required>
                        </div>
                        <div>
                            <label for="customer_country" class="block text-blue-100 mb-2">Pays</label>
                            <select class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-white focus:border-white text-white" 
                                    name="customer_country" id="customer_country" required>
                                <option value="CI">Côte d'Ivoire</option>
                                <option value="BF">Burkina Faso</option>
                                <option value="SN">Sénégal</option>
                                <option value="ML">Mali</option>
                                <option value="NE">Niger</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" onclick="checkout()" 
                                class="w-full py-4 bg-white text-blue-700 rounded-xl font-bold hover:bg-blue-50 transition flex items-center justify-center">
                            <i class="fas fa-heart mr-3"></i> Faire un don maintenant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section - Version améliorée -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <span class="inline-block px-4 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold mb-4">
                    Contact
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Nous contacter</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Une question, une suggestion ou besoin d'aide ? Écrivez-nous.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-10">
                <div class="bg-white rounded-2xl shadow-lg p-8 h-full">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Envoyez-nous un message</h3>
                    
                    @if (Session::has('messages'))
                    <div class="p-4 mb-6 rounded-lg bg-green-100 text-green-700">
                        {{ Session::get('messages') }}
                    </div>
                    @endif
                    
                    <form action="{{ url('contact-us') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label for="name" class="block text-gray-700 mb-2">Votre nom</label>
                            <input type="text" class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400" 
                                   name="name" placeholder="Votre nom complet" required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-gray-700 mb-2">Votre email</label>
                            <input type="email" class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400" 
                                   name="email" placeholder="email@exemple.com" required>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-gray-700 mb-2">Votre message</label>
                            <textarea class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400" 
                                      name="message" rows="5" placeholder="Écrivez votre message ici..." required></textarea>
                        </div>
                        
                        <div>
                            <button type="submit" class="px-8 py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition w-full">
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="bg-blue-600 rounded-2xl shadow-lg p-8 text-white h-full">
                    <h3 class="text-2xl font-bold mb-6">Nos coordonnées</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-xl mr-4 mt-1">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg mb-1">Adresse</h4>
                                <p class="text-blue-100">Plateau, Abidjan, Côte d'Ivoire</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-xl mr-4 mt-1">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg mb-1">Téléphone</h4>
                                <p class="text-blue-100">+225 XX XX XX XX</p>
                                <p class="text-blue-100">+225 XX XX XX XX</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-xl mr-4 mt-1">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg mb-1">Email</h4>
                                <p class="text-blue-100">contact@qct.ci</p>
                                <p class="text-blue-100">support@qct.ci</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-xl mr-4 mt-1">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg mb-1">Heures d'ouverture</h4>
                                <p class="text-blue-100">Lundi - Vendredi: 8h - 18h</p>
                                <p class="text-blue-100">Samedi: 9h - 13h</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-white/20">
                        <h4 class="font-bold text-lg mb-4">Suivez-nous</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl hover:bg-white/20 transition">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl hover:bg-white/20 transition">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl hover:bg-white/20 transition">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-xl hover:bg-white/20 transition">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts -->
<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
  function checkout() {
    var amount = document.getElementById('amount').value;
    var customer_name = document.getElementById('customer_name').value;
    var customer_surname = document.getElementById('customer_surname').value;
    var customer_email = document.getElementById('customer_email').value;
    var customer_phone_number = document.getElementById('customer_phone_number').value;
    var customer_address = document.getElementById('customer_address').value;
    var customer_city = document.getElementById('customer_city').value;
    var customer_country = document.getElementById('customer_country').value;
    
    CinetPay.setConfig({
        apikey: '{{ env('CINETPAY_API_KEY') }}',
        site_id: '{{ env('CINETPAY_SITE_ID') }}',
        notify_url: 'http://127.0.0.1:8000/add-item',
        mode: 'PRODUCTION'
    });
    
    CinetPay.getCheckout({
        "transaction_id": Math.floor(Math.random() * 100000000).toString(),
        "amount": amount,
        "currency": "XOF",
        "description": "Donation pour QCT",
        "customer_id": "user_" + Math.floor(Math.random() * 10000),
        "customer_name": customer_name,
        "customer_surname": customer_surname,
        "customer_email": customer_email,
        "customer_phone_number": customer_phone_number,
        "customer_address": customer_address,
        "customer_city": customer_city,
        "customer_country": customer_country,
        "customer_state": "CI",
        "customer_zip_code": "",
        "channels": "ALL",
    });
    
    CinetPay.waitResponse(function(data) {
        if (data.status == "REFUSED") {
            Swal.fire({
                icon: 'error',
                title: 'Paiement échoué',
                text: 'Votre paiement n\'a pas abouti. Veuillez réessayer.',
                confirmButtonColor: '#3B82F6'
            });
        } else if (data.status == "ACCEPTED") {
            Swal.fire({
                icon: 'success',
                title: 'Merci pour votre don!',
                text: 'Votre paiement a été effectué avec succès.',
                confirmButtonColor: '#10B981'
            });
        }
    });
    
    CinetPay.onError(function(data) {
        console.log(data);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors du traitement de votre paiement.',
            confirmButtonColor: '#3B82F6'
        });
    });
  }
</script>
@endsection