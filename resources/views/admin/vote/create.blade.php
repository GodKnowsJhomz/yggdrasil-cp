@extends('layouts.app')

@section('title', 'Novo Site de Votação - Admin')

@section('content')
<div class="max-w-[1200px] mx-auto p-5">
    <div class="mb-6">
        <a href="{{ route('admin.vote.index') }}" class="text-brand-main hover:underline font-robotoCond">← Voltar para gerenciar sites</a>
    </div>

    <h1 class="text-4xl font-bold font-robotoCond text-brand-main mb-8">Novo Site de Votação</h1>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.vote.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8 max-w-full">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Site *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-brand-main focus:border-brand-main"
                    placeholder="Ex: TopRO 100">
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL do Site *</label>
                <input type="url" name="url" id="url" value="{{ old('url') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-brand-main focus:border-brand-main"
                    placeholder="https://topro100.com/vote">
            </div>

            <div>
                <label for="points" class="block text-sm font-medium text-gray-700 mb-2">Pontos por Voto *</label>
                <input type="number" name="points" id="points" value="{{ old('points', 1) }}" required min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-brand-main focus:border-brand-main"
                    placeholder="Ex: 5">
            </div>

            <div>
                <label for="block_timer" class="block text-sm font-medium text-gray-700 mb-2">Tempo de Bloqueio (segundos) *</label>
                <input type="number" name="block_timer" id="block_timer" value="{{ old('block_timer', 43200) }}" required min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-brand-main focus:border-brand-main"
                    placeholder="Ex: 43200 (12 horas)">
                <p class="text-sm text-gray-500 mt-1">12 horas = 43200 segundos | 24 horas = 86400 segundos</p>
            </div>
        </div>

        <div class="mt-6">
            <label for="cover" class="block text-sm font-medium text-gray-700 mb-2">Imagem/Logo</label>
            <input type="file" name="cover" id="cover" accept="image/*"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-brand-main focus:border-brand-main">
            <p class="text-sm text-gray-500 mt-1">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</p>
            
            <div id="cover-preview" class="mt-4 hidden">
                <p class="text-sm text-gray-600 mb-2">Preview:</p>
                <img src="" alt="Preview" class="max-w-xs rounded-md shadow">
            </div>
        </div>

        <div class="mt-6">
            <label class="flex items-center">
                <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-brand-main shadow-sm focus:border-brand-main focus:ring focus:ring-brand-main focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Site ativo (visível para os jogadores)</span>
            </label>
        </div>

        <div class="flex gap-4 mt-8">
            <button type="submit" class="bg-brand-main text-white px-6 py-3 rounded-md font-robotoCond font-bold hover:bg-brand-green transition-colors">
                Criar Site
            </button>
            <a href="{{ route('admin.vote.index') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-md font-robotoCond font-bold hover:bg-gray-400 transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('cover').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('cover-preview');
            const img = preview.querySelector('img');
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
