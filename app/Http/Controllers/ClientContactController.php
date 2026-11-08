<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Http\Request;

class ClientContactController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'is_primary' => 'boolean',
        ]);

        if ($request->has('is_primary') && $request->boolean('is_primary')) {
            $client->contacts()->update(['is_primary' => false]);
            $validated['is_primary'] = true;
        } else {
            $validated['is_primary'] = false;
        }

        $client->contacts()->create($validated);

        return redirect()->route('clients.show', ['client' => $client->id, 'tab' => 'contacts'])
            ->with('success', 'Contacto agregado exitosamente.');
    }

    public function destroy(Client $client, ClientContact $contact)
    {
        $contact->delete();

        return redirect()->route('clients.show', ['client' => $client->id, 'tab' => 'contacts'])
            ->with('success', 'Contacto eliminado correctamente.');
    }
}
