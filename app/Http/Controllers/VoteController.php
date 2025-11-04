<?php

namespace App\Http\Controllers;

use App\Models\VoteSite;
use App\Models\VoteBlock;
use App\Models\VotePoint;
use App\Models\User;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    // Página pública de votação
    public function index(Request $request)
    {
        if (!session('user_id')) {
            return redirect('/account')->with('message', 'Você precisa estar logado para votar.')
                ->with('message_type', 'error');
        }

        $sites = VoteSite::where('active', true)->orderBy('name')->get();
        $userId = session('user_id');
        $ipAddress = $this->getIpAddress();

        // Adicionar informações de bloqueio para cada site
        foreach ($sites as $site) {
            $site->can_vote = $site->canVote($userId, $ipAddress);
            $site->time_left = $site->getTimeLeft($userId, $ipAddress);
        }

        $userPoints = VotePoint::getPoints($userId);

        return view('vote.index', compact('sites', 'userPoints'));
    }

    // Processar voto
    public function vote(Request $request, $id)
    {
        if (!session('user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa estar logado para votar.'
            ], 401);
        }

        $site = VoteSite::findOrFail($id);
        $userId = session('user_id');
        $ipAddress = $this->getIpAddress();

        if (!$site->active) {
            return response()->json([
                'success' => false,
                'message' => 'Este site de votação está desativado.'
            ], 400);
        }

        if (!$site->canVote($userId, $ipAddress)) {
            return response()->json([
                'success' => false,
                'message' => 'Você já votou neste site recentemente. Aguarde: ' . $site->getTimeLeft($userId, $ipAddress),
                'time_left' => $site->getTimeLeft($userId, $ipAddress)
            ], 429);
        }

        $currentTime = time();

        // Registrar o voto
        VoteBlock::updateOrCreate(
            [
                'vote_site_id' => $site->id,
                'user_id' => $userId
            ],
            [
                'ip_address' => $ipAddress,
                'last_timer' => $currentTime
            ]
        );

        // Adicionar pontos
        VotePoint::addPoints($userId, $site->points);

        return response()->json([
            'success' => true,
            'message' => "Você ganhou {$site->points} pontos! Redirecionando para o site...",
            'url' => $site->url,
            'points' => $site->points,
            'time_left' => $site->getTimeLeft($userId, $ipAddress),
            'total_points' => VotePoint::getPoints($userId)
        ]);
    }

    // Admin: Listar sites
    public function adminIndex()
    {
        $this->authorizeAdmin();
        $sites = VoteSite::orderBy('name')->paginate(20);
        return view('admin.vote.index', compact('sites'));
    }

    // Admin: Criar site
    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.vote.create');
    }

    // Admin: Salvar novo site
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'url' => 'required|url|max:250',
            'cover' => 'nullable|image|max:2048',
            'points' => 'required|integer|min:1',
            'block_timer' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover'] = $request->file('cover')->store('vote-covers', 'public');
        }

        VoteSite::create($validated);

        return redirect()->route('admin.vote.index')
            ->with('success', 'Site de votação criado com sucesso!');
    }

    // Admin: Editar site
    public function edit(VoteSite $vote)
    {
        $this->authorizeAdmin();
        return view('admin.vote.edit', compact('vote'));
    }

    // Admin: Atualizar site
    public function update(Request $request, VoteSite $vote)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'url' => 'required|url|max:250',
            'cover' => 'nullable|image|max:2048',
            'points' => 'required|integer|min:1',
            'block_timer' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('cover')) {
            if ($vote->cover) {
                \Storage::disk('public')->delete($vote->cover);
            }
            $validated['cover'] = $request->file('cover')->store('vote-covers', 'public');
        }

        $vote->update($validated);

        return redirect()->route('admin.vote.index')
            ->with('success', 'Site de votação atualizado com sucesso!');
    }

    // Admin: Deletar site
    public function destroy(VoteSite $vote)
    {
        $this->authorizeAdmin();

        if ($vote->cover) {
            \Storage::disk('public')->delete($vote->cover);
        }

        $vote->delete();

        return redirect()->route('admin.vote.index')
            ->with('success', 'Site de votação deletado com sucesso!');
    }

    // Helpers
    private function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
    }

    private function authorizeAdmin()
    {
        if (!session('user_id')) {
            abort(403, 'Você precisa estar logado para acessar esta página.');
        }

        $user = User::find(session('user_id'));
        
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
        }
    }
}
