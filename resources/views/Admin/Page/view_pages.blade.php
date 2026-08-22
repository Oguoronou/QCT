@extends('Admin.layout')
@section('content')

<main class="p-6">
    <div class="flex flex-col mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pages de contenu</h1>
        <p class="text-gray-600 mt-1">FAQ, Comment ça marche, Politique de confidentialité, CGU, CGV</p>
    </div>

    @if(Session::has('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start">
        <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
        <p>{{ Session::get('message') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                Liste des pages
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière modification</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($pages as $page)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $page->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->updated_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ url('admin/pages/'.$page->slug.'/edit') }}"
                               class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-full hover:bg-blue-50"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

@endsection
