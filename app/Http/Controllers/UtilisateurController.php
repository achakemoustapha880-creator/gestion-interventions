<?php

namespace App\Http\Controllers;

use App\Models\Technicien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UtilisateurController extends Controller
{
    private function messages(): array
    {
        return ['technicien_id.required_if' => 'Choisissez la fiche technicien liée à ce compte.'];
    }

    public function index()
    {
        $utilisateurs = User::orderBy('name')->get();

        return view('utilisateurs.index', compact('utilisateurs'));
    }

    public function create()
    {
        return view('utilisateurs.form', [
            'utilisateur' => new User(['role' => 'technicien']),
            'techniciens' => Technicien::orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => ['required', Password::min(8)],
            'role'          => ['required', Rule::in(['admin', 'technicien'])],
            'technicien_id' => ['nullable', 'required_if:role,technicien', 'exists:techniciens,id'],
        ], $this->messages());

        $utilisateur = new User(collect($data)->only(['name', 'email', 'password'])->all());
        $utilisateur->role = $data['role'];
        $utilisateur->technicien_id = $data['role'] === 'technicien' ? $data['technicien_id'] : null;
        $utilisateur->save();

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(User $utilisateur)
    {
        return view('utilisateurs.form', [
            'utilisateur' => $utilisateur,
            'techniciens' => Technicien::orderBy('nom')->get(),
        ]);
    }

    public function update(Request $request, User $utilisateur)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($utilisateur->id)],
            'password'      => ['nullable', Password::min(8)],
            'role'          => ['required', Rule::in(['admin', 'technicien'])],
            'technicien_id' => ['nullable', 'required_if:role,technicien', 'exists:techniciens,id'],
        ], $this->messages());

        $utilisateur->name  = $data['name'];
        $utilisateur->email = $data['email'];

        if (! empty($data['password'])) {
            $utilisateur->password = $data['password'];
        }

        // On ne modifie pas son propre rôle (évite de se retirer l'accès administrateur)
        if ($utilisateur->id !== $request->user()->id) {
            $utilisateur->role = $data['role'];
            $utilisateur->technicien_id = $data['role'] === 'technicien' ? $data['technicien_id'] : null;
        }

        $utilisateur->save();

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(Request $request, User $utilisateur)
    {
        if ($utilisateur->id === $request->user()->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $utilisateur->delete();

        return redirect()->route('utilisateurs.index')->with('success', 'Utilisateur supprimé.');
    }
}