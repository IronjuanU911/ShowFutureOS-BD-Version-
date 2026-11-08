<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientAccessController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:100',
            'url' => 'nullable|url|max:500',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $updateData = [
            'access_platform' => $validated['platform'],
            'access_url' => $validated['url'] ?? null,
            'access_username' => $validated['username'] ?? null,
            'access_notes' => $validated['notes'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['access_password'] = $validated['password'];
        }

        $client->update($updateData);

        return redirect()->route('clients.show', ['client' => $client->id, 'tab' => 'accesses'])
            ->with('success', 'Credencial de acceso actualizada exitosamente.');
    }

    public function destroy(Client $client)
    {
        $client->update([
            'access_platform' => null,
            'access_url' => null,
            'access_username' => null,
            'access_password' => null,
            'access_notes' => null,
        ]);

        return redirect()->route('clients.show', ['client' => $client->id, 'tab' => 'accesses'])
            ->with('success', 'Credencial de acceso eliminada de la cuenta.');
    }
}
