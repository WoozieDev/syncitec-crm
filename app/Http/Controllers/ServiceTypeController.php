<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceTypes\StoreServiceTypeRequest;
use App\Http\Requests\ServiceTypes\UpdateServiceTypeRequest;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $serviceTypes = ServiceType::query()
            ->withCount('services')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ServiceType $serviceType) => $this->serviceTypeData($serviceType));

        return Inertia::render('serviceTypes/Index', [
            'service_types' => $serviceTypes,
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
        return Inertia::render('serviceTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceTypeRequest $request): RedirectResponse
    {
        ServiceType::create($request->validated());

        return to_route('serviceTypes.index')->with('success', 'Tipo de servicio creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceType $serviceType): RedirectResponse
    {
        return to_route('serviceTypes.edit', $serviceType);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceType $serviceType): Response
    {
        $serviceType->loadCount('services');

        return Inertia::render('serviceTypes/Edit', [
            'service_type' => $this->serviceTypeData($serviceType),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceTypeRequest $request, ServiceType $serviceType): RedirectResponse
    {
        $serviceType->update($request->validated());

        return to_route('serviceTypes.index')->with('success', 'Tipo de servicio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceType $serviceType): RedirectResponse
    {
        $serviceType->loadCount('services');

        if ($serviceType->services_count > 0) {
            return to_route('serviceTypes.index')->with(
                'error',
                'No se puede eliminar este tipo porque tiene servicios asociados.'
            );
        }

        $serviceType->delete();

        return to_route('serviceTypes.index')->with('success', 'Tipo de servicio eliminado correctamente.');
    }

    /**
     * @return array{id: int, name: string, services_count: int, created_at: string|null, updated_at: string|null}
     */
    private function serviceTypeData(ServiceType $serviceType): array
    {
        return [
            'id' => $serviceType->id,
            'name' => $serviceType->name,
            'services_count' => (int) ($serviceType->services_count ?? 0),
            'created_at' => $serviceType->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $serviceType->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
