@extends('layouts.app')

@section('title', 'Gerenciar Sites de Votação - Admin')

@section('content')
<div class="max-w-[1400px] mx-auto p-5">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold font-robotoCond text-brand-main">Gerenciar Sites de Votação</h1>
            <p class="text-gray-600 mt-2">Adicione, edite ou remova sites de votação para recompensar seus jogadores</p>
        </div>
        <a href="{{ route('admin.vote.create') }}" class="bg-brand-main text-white px-6 py-3 rounded-md font-robotoCond font-bold hover:bg-brand-green transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Novo Site
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center">
        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Timer</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sites as $site)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($site->cover)
                            <img src="{{ asset('storage/' . $site->cover) }}" alt="{{ $site->name }}" class="h-12 w-12 rounded object-cover mr-3 border border-gray-200">
                            @else
                            <div class="h-12 w-12 rounded bg-gray-200 mr-3 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $site->name }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $site->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <a href="{{ $site->url }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Visitar
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            {{ $site->points }} pts
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                        <div class="flex flex-col">
                            <span class="font-mono font-bold">{{ gmdate('H:i:s', $site->block_timer) }}</span>
                            <span class="text-xs text-gray-500">
                                @if($site->block_timer >= 86400)
                                    {{ floor($site->block_timer / 86400) }} dia(s)
                                @elseif($site->block_timer >= 3600)
                                    {{ floor($site->block_timer / 3600) }} hora(s)
                                @else
                                    {{ floor($site->block_timer / 60) }} min
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $site->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $site->active ? '✓ Ativo' : '✗ Inativo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium w-48">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.vote.edit', $site) }}" 
                               style="background-color: #2563eb; color: white; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; display: inline-flex; align-items: center; text-decoration: none;"
                               onmouseover="this.style.backgroundColor='#1d4ed8'" 
                               onmouseout="this.style.backgroundColor='#2563eb'">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('admin.vote.destroy', $site) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Tem certeza que deseja excluir o site \'{{ $site->name }}\'?\n\nEsta ação não pode ser desfeita!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        style="background-color: #dc2626; color: white; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; display: inline-flex; align-items: center; border: none; cursor: pointer;"
                                        onmouseover="this.style.backgroundColor='#b91c1c'" 
                                        onmouseout="this.style.backgroundColor='#dc2626'">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.25rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mt-4 text-gray-500 text-lg">Nenhum site de votação cadastrado</p>
                        <p class="mt-2 text-gray-400">Comece adicionando o primeiro site para seus jogadores votarem</p>
                        <a href="{{ route('admin.vote.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-brand-main text-white rounded-md hover:bg-brand-green transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Criar Primeiro Site
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sites->hasPages())
    <div class="mt-6">
        {{ $sites->links() }}
    </div>
    @endif
</div>
@endsection
