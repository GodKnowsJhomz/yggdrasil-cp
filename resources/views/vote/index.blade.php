@extends('layouts.app')

@section('title', 'Sistema de Votação - Myth of Yggdrasil')
@section('description', 'Vote no servidor e ganhe recompensas!')

@section('content')
<div class="max-w-[1200px] mx-auto p-5">
    <header class="text-center mb-10">
        <h1 class="text-4xl md:text-5xl font-bold font-robotoCond text-brand-main mb-4">Sistema de Votação</h1>
        <p class="text-lg text-gray-600">Vote nos sites abaixo e ganhe pontos para trocar por recompensas!</p>
        <div class="mt-4 bg-brand-main text-white px-6 py-3 rounded-lg inline-block">
            <span class="font-bold text-xl">Seus Pontos: {{ $userPoints }}</span>
        </div>
    </header>

    @if(session('message'))
    <div class="mb-6 p-4 rounded-md {{ session('message_type') === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
        {{ session('message') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sites as $site)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            @if($site->cover)
            <div class="h-48 bg-gray-200">
                <img src="{{ asset('storage/' . $site->cover) }}" alt="{{ $site->name }}" class="w-full h-full object-cover">
            </div>
            @else
            <div class="h-48 bg-gradient-to-br from-brand-main to-brand-green flex items-center justify-center">
                <span class="text-white text-4xl font-bold">{{ substr($site->name, 0, 1) }}</span>
            </div>
            @endif
            
            <div class="p-6">
                <h2 class="text-2xl font-bold font-robotoCond text-brand-main mb-2">{{ $site->name }}</h2>
                <p class="text-gray-600 mb-4">
                    <span class="font-bold text-brand-green">+{{ $site->points }} pontos</span>
                </p>
                
                @if($site->can_vote)
                <button onclick="vote({{ $site->id }}, '{{ $site->name }}')" 
                    class="w-full bg-brand-main text-white px-4 py-3 rounded-md font-bold hover:bg-brand-green transition-colors vote-btn"
                    data-site-id="{{ $site->id }}">
                    Votar Agora
                </button>
                @else
                <div class="text-center">
                    <div class="bg-gray-200 text-gray-600 px-4 py-3 rounded-md font-bold mb-2">
                        Aguarde
                    </div>
                    <p class="text-sm text-gray-500">
                        Próximo voto em: <span class="font-mono font-bold time-left" data-site-id="{{ $site->id }}">{{ $site->time_left }}</span>
                    </p>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-10">
            <p class="text-gray-500 text-lg">Nenhum site de votação disponível no momento.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function vote(siteId, siteName) {
    const btn = document.querySelector(`.vote-btn[data-site-id="${siteId}"]`);
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Processando...';
    
                fetch(`/account/votes/${siteId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Abrir site de votação em nova aba
            window.open(data.url, '_blank');
            // Recarregar página após 1 segundo
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Ocorreu um erro ao processar seu voto. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Atualizar contadores de tempo a cada segundo
setInterval(() => {
    document.querySelectorAll('.time-left').forEach(el => {
        const timeStr = el.textContent;
        const parts = timeStr.split(':');
        if (parts.length === 3) {
            let hours = parseInt(parts[0]);
            let minutes = parseInt(parts[1]);
            let seconds = parseInt(parts[2]);
            
            if (seconds > 0) {
                seconds--;
            } else if (minutes > 0) {
                minutes--;
                seconds = 59;
            } else if (hours > 0) {
                hours--;
                minutes = 59;
                seconds = 59;
            } else {
                // Tempo acabou, recarregar página
                window.location.reload();
                return;
            }
            
            el.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
    });
}, 1000);
</script>
@endsection
