<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $clients = Client::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Client $client) => $this->clientData($client));

        return Inertia::render('clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());

        return to_route('clients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): Response
    {
        return Inertia::render('clients/Show', [
            'client' => $this->clientData($client),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): Response
    {
        return Inertia::render('clients/Edit', [
            'client' => $this->clientData($client),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return to_route('clients.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return to_route('clients.index');
    }

    /**
     * Normalizes the data exposed to customer views.
     *
     * @return array<string, mixed>
     */
    private function clientData(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'company' => $client->company,
            'email' => $client->email,
            'phone' => $client->phone,
            'country' => $client->country,
            'notes' => $client->notes,
            'created_at' => $client->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $client->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
