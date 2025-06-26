<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Application QCT">
  
  <title>QCT</title>

  <!-- Favicons -->
  <link href="{{ asset('customerdesign/assets/img/favicon.png')}}" rel="icon">
  <link href="{{ asset('customerdesign/assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  @vite('resources/css/app.css')
</head>

<body class="font-sans bg-gray-50 text-gray-800">
  <!-- Header -->
  <header class="fixed top-0 left-0 right-0 bg-white shadow-sm z-40 h-16 flex items-center px-4">
    <div class="flex items-center justify-between w-full">
      <!-- Logo -->
      <div class="flex items-center">
        <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600 hidden lg:block">QCT</a>
        <button id="sidebarToggle" class="ml-4 text-gray-600 lg:hidden">
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>

      <!-- Search Bar -->
      <div class="hidden md:block mx-4 flex-1 max-w-md">
        <form class="relative">
          <input type="text" placeholder="Rechercher..." 
                 class="w-full pl-4 pr-10 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          <button type="submit" class="absolute right-3 top-2 text-gray-500 hover:text-blue-600">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>

      <!-- User Profile -->
      <div class="relative">
        <button id="profileDropdownButton" class="flex items-center space-x-2 focus:outline-none">
          <img src="{{ asset('customerdesign/assets/img/profile-img.jpg')}}" alt="Profile" class="w-8 h-8 rounded-full">
          <span class="hidden md:inline-block font-medium">Zahid Akbar</span>
          <i class="fas fa-chevron-down hidden md:inline-block text-sm"></i>
        </button>

        <!-- Dropdown Menu -->
        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
          <div class="px-4 py-2 border-b">
            <h6 class="font-semibold">Zahid Akbar</h6>
          </div>
          <a href="#" class="block px-4 py-2 hover:bg-gray-100">
            <i class="fas fa-user mr-2"></i> Mon Profil
          </a>
          @if(!empty(Auth::user()))
          <a href="{{ url('logout') }}" class="block px-4 py-2 hover:bg-gray-100">
            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
          </a>
          @else
          <div class="px-4 py-2 space-x-2">
            <a href="{{ url('register') }}" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Inscription</a>
            <a href="{{ url('login') }}" class="text-sm bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Connexion</a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside id="sidebar" class="fixed top-16 left-0 bottom-0 w-64 bg-white shadow-md transform -translate-x-full lg:translate-x-0 transition-transform duration-200 z-30">
    <nav class="h-full overflow-y-auto py-4 px-3">
      <ul class="space-y-2">
        <li>
          <a href="{{ url('admin/dashboard') }}" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <i class="fas fa-tachometer-alt w-6 text-center"></i>
            <span class="ml-3">Dashboard</span>
          </a>
        </li>
        
        <li>
          <a href="{{ url('admin/profile') }}" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <i class="fas fa-user w-6 text-center"></i>
            <span class="ml-3">Profil</span>
          </a>
        </li>

        <!-- Category Dropdown -->
        <li>
          <button id="categoryDropdown" class="flex items-center justify-between w-full p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <div class="flex items-center">
              <i class="fas fa-list-alt w-6 text-center"></i>
              <span class="ml-3">Catégories</span>
            </div>
            <i class="fas fa-chevron-down text-xs"></i>
          </button>
          <ul id="categorySubmenu" class="hidden py-2 space-y-1 pl-11">
            <li>
              <a href="{{ url('admin/categories') }}" class="flex items-center p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded text-sm">
                <i class="fas fa-circle text-xs mr-2"></i>
                Liste des catégories
              </a>
            </li>
            <li>
              <a href="{{ url('admin/add-category') }}" class="flex items-center p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded text-sm">
                <i class="fas fa-circle text-xs mr-2"></i>
                Ajouter une catégorie
              </a>
            </li>
          </ul>
        </li>

        <li>
          <a href="{{ url('admin/messages') }}" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <i class="fas fa-envelope w-6 text-center"></i>
            <span class="ml-3">Messages</span>
          </a>
        </li>

        <li>
          <a href="{{ url('admin/lost-and-found') }}" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <i class="fas fa-box-open w-6 text-center"></i>
            <span class="ml-3">Objets perdus</span>
          </a>
        </li>

        @if(!empty(Auth::user()))
        <li class="pt-4 border-t mt-4">
          <a href="{{ url('logout') }}" class="flex items-center p-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded">
            <i class="fas fa-sign-out-alt w-6 text-center"></i>
            <span class="ml-3">Déconnexion</span>
          </a>
        </li>
        @endif
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="lg:ml-64 pt-16 min-h-screen bg-gray-50">
    <div class="p-6">
      @yield('content')
    </div>
  </main>

  <!-- Footer -->
  <footer class="lg:ml-64 bg-white py-4 px-6 border-t">
    <div class="text-center text-gray-600 text-sm">
      &copy; Copyright <strong class="font-semibold">TLost</strong>. Tous droits réservés
    </div>
  </footer>

  <!-- Back to Top Button -->
  <a href="#" class="fixed bottom-4 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
    <i class="fas fa-arrow-up"></i>
  </a>

  <!-- Scripts -->
  <script>
    // Toggle sidebar on mobile
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('-translate-x-full');
    });

    // Toggle profile dropdown
    document.getElementById('profileDropdownButton').addEventListener('click', function() {
      document.getElementById('profileDropdown').classList.toggle('hidden');
    });

    // Toggle category dropdown
    document.getElementById('categoryDropdown').addEventListener('click', function() {
      document.getElementById('categorySubmenu').classList.toggle('hidden');
      this.querySelector('i:last-child').classList.toggle('transform');
      this.querySelector('i:last-child').classList.toggle('rotate-180');
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('#profileDropdownButton') && !e.target.closest('#profileDropdown')) {
        document.getElementById('profileDropdown').classList.add('hidden');
      }
    });
  </script>
</body>
</html>