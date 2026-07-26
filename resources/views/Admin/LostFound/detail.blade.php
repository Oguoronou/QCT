@extends('Admin.layout')
@section('content')

<main class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Détails de l'objet</h1>
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
                                <a href="{{ url('my-items') }}" class="text-sm text-gray-600 hover:text-blue-600">Mes objets</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                                <span class="text-sm font-medium text-blue-600 ml-1">Détails</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            <a href="{{ url('my-items') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Card Header -->
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                Informations détaillées
            </h3>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button class="mr-8 py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Aperçu
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-6">
                <!-- Item Details Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Item Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>
                            Détails de l'objet
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">ID:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->id ?? "12312" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Date de création:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->created_at ?? "1h" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Nom:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->item_name ?? "12312" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Catégorie:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->category_name ?? "12312" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Type:</div>
                                <div class="col-span-2 text-sm text-gray-800">
                                    <span class="px-2 py-1 rounded-full text-xs 
                                        {{ $item->status == 'found' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $item->status ?? "pending" }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Statut:</div>
                                <div class="col-span-2 text-sm text-gray-800">
                                    <span class="px-2 py-1 rounded-full text-xs 
                                        {{ $item->lost_found_status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $item->lost_found_status ?? "pending" }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Date perdu/trouvé:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->date ?? "buy" }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-blue-600"></i>
                            Détails de l'utilisateur
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Nom:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->name ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Email:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->email ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Téléphone:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->mobile_no ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Pays:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->country ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Ville:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->city ?? "" }}</div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-500">Adresse:</div>
                                <div class="col-span-2 text-sm text-gray-800">{{ $item->user->address ?? "" }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-align-left mr-2 text-blue-600"></i>
                        Description
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Labore sit repellendus ducimus nobis tempore hic soluta porro voluptatibus dolor debitis! Architecto quaerat saepe dignissimos iste ipsam neque cumque nemo autem. Lorem ipsum dolor sit amet consectetur adipisicing elit. Distinctio a, voluptatibus, earum laudantium possimus nisi libero necessitatibus, omnis harum saepe laboriosam repudiandae veritatis? Aliquam similique necessitatibus alias hic labore voluptates?
                        </p>
                    </div>
                </div>

                <!-- Image Section -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-image mr-2 text-blue-600"></i>
                        Image
                    </h4>
                    <div class="bg-gray-100 p-4 rounded-lg flex justify-center">
                        <img src="{{ asset('images/'.$item->image) }}" alt="Image de l'objet" class="max-w-full h-auto rounded-lg shadow-sm max-h-96">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection