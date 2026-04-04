<?php

namespace App\Http\Controllers;

use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Models\Client;
use App\Models\Provider;
use App\Models\Service;
use App\Models\ServiceType;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    private const BILLING_TYPE_LABELS = [
        'unico' => 'Pago unico',
        'recurrente' => 'Recurrente',
    ];

    private const BILLING_CYCLE_LABELS = [
        'mensual' => 'Mensual',
        'trimestral' => 'Trimestral',
        'semestral' => 'Semestral',
        'anual' => 'Anual',
    ];

    private const STATUS_PRESENTATIONS = [
        'activo' => [
            'label' => 'Activo',
            'tone' => 'active',
        ],
        'pendiente' => [
            'label' => 'Pendiente',
            'tone' => 'pending',
        ],
        'suspendido' => [
            'label' => 'Suspendido',
            'tone' => 'suspended',
        ],
        'cancelado' => [
            'label' => 'Cancelado',
            'tone' => 'canceled',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $clientId = $request->integer('client_id');
        $status = $request->string('status')->trim()->toString();
        $serviceTypeId = $request->integer('service_type_id');

        $baseQuery = Service::query()
            ->with([
                'client:id,name,company',
                'serviceType:id,name',
                'provider:id,name',
            ])
            ->when($search !== '', fn (Builder $query) => $query->search($search))
            ->when($clientId > 0, fn (Builder $query) => $query->where('client_id', $clientId))
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($serviceTypeId > 0, fn (Builder $query) => $query->where('service_type_id', $serviceTypeId));

        $services = (clone $baseQuery)
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Service $service) => $this->serviceData($service));

        return Inertia::render('services/Index', [
            'services' => $services,
            'filters' => [
                'search' => $search,
                'client_id' => $clientId > 0 ? (string) $clientId : '',
                'status' => $status,
                'service_type_id' => $serviceTypeId > 0 ? (string) $serviceTypeId : '',
            ],
            'overview' => $this->overviewData($baseQuery),
            'clients' => $this->clientOptions(),
            'service_types' => $this->serviceTypeOptions(),
            'status_options' => $this->statusOptions(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('services/Create', [
            'clients' => $this->clientOptions(),
            'service_types' => $this->serviceTypeOptions(),
            'providers' => $this->providerOptions(),
            'billing_type_options' => $this->billingTypeOptions(),
            'billing_cycle_options' => $this->billingCycleOptions(),
            'status_options' => $this->statusOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        Service::create($request->validated());

        return to_route('services.index')->with('success', 'Servicio creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): Response
    {
        $service->load([
            'client:id,name,company,email,phone,country',
            'serviceType:id,name',
            'provider:id,name',
        ]);

        return Inertia::render('services/Show', [
            'service' => $this->serviceDetailData($service),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): Response
    {
        $service->load([
            'client:id,name,company',
            'serviceType:id,name',
            'provider:id,name',
        ]);

        return Inertia::render('services/Edit', [
            'service' => $this->serviceData($service),
            'clients' => $this->clientOptions(),
            'service_types' => $this->serviceTypeOptions(),
            'providers' => $this->providerOptions(),
            'billing_type_options' => $this->billingTypeOptions(),
            'billing_cycle_options' => $this->billingCycleOptions(),
            'status_options' => $this->statusOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return to_route('services.index')->with('success', 'Servicio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return to_route('services.index')->with('success', 'Servicio eliminado correctamente.');
    }

    /**
     * @return array{total_services: int, active_services: int, expiring_services: int, expired_services: int}
     */
    private function overviewData(Builder $baseQuery): array
    {
        $today = Carbon::today();
        $upcomingLimit = $today->copy()->addDays(30);

        return [
            'total_services' => (clone $baseQuery)->count(),
            'active_services' => (clone $baseQuery)->where('status', 'activo')->count(),
            'expiring_services' => (clone $baseQuery)
                ->whereNotNull('next_renewal_date')
                ->whereDate('next_renewal_date', '>=', $today->toDateString())
                ->whereDate('next_renewal_date', '<=', $upcomingLimit->toDateString())
                ->count(),
            'expired_services' => (clone $baseQuery)
                ->whereNotNull('next_renewal_date')
                ->whereDate('next_renewal_date', '<', $today->toDateString())
                ->count(),
        ];
    }

    /**
     * @return array<int, array{id: int, name: string, company: string|null, label: string}>
     */
    private function clientOptions(): array
    {
        return Client::query()
            ->orderBy('company')
            ->orderBy('name')
            ->get(['id', 'name', 'company'])
            ->map(fn (Client $client) => [
                'id' => $client->id,
                'name' => $client->name,
                'company' => $client->company,
                'label' => $client->company
                    ? "{$client->company} - {$client->name}"
                    : $client->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string, label: string}>
     */
    private function serviceTypeOptions(): array
    {
        return ServiceType::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (ServiceType $serviceType) => [
                'id' => $serviceType->id,
                'name' => $serviceType->name,
                'label' => $serviceType->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string, label: string}>
     */
    private function providerOptions(): array
    {
        return Provider::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Provider $provider) => [
                'id' => $provider->id,
                'name' => $provider->name,
                'label' => $provider->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function billingTypeOptions(): array
    {
        return collect(Service::BILLING_TYPES)
            ->map(fn (string $value) => [
                'value' => $value,
                'label' => $this->billingTypeLabel($value),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function billingCycleOptions(): array
    {
        return collect(Service::BILLING_CYCLES)
            ->map(fn (string $value) => [
                'value' => $value,
                'label' => $this->billingCycleLabel($value),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return collect(Service::STATUSES)
            ->map(fn (string $value) => [
                'value' => $value,
                'label' => $this->statusPresentation($value)['label'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     description: string|null,
     *     billing_type: string,
     *     billing_type_label: string,
     *     billing_cycle: string|null,
     *     billing_cycle_label: string|null,
     *     price: float,
     *     cost: float|null,
     *     start_date: string|null,
     *     next_renewal_date: string|null,
     *     renewal_state: string,
     *     renewal_label: string,
     *     status: string,
     *     status_label: string,
     *     status_tone: string,
     *     notes: string|null,
     *     client: array{id: int|null, name: string|null, company: string|null, display_name: string},
     *     service_type: array{id: int|null, name: string|null},
     *     provider: array{id: int|null, name: string|null}|null,
     *     created_at: string|null,
     *     updated_at: string|null
     * }
     */
    private function serviceData(Service $service): array
    {
        $status = strtolower((string) $service->status);
        $statusPresentation = $this->statusPresentation($status);
        $renewalPresentation = $this->renewalPresentation($service->next_renewal_date);

        return [
            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description,
            'billing_type' => (string) $service->billing_type,
            'billing_type_label' => $this->billingTypeLabel((string) $service->billing_type),
            'billing_cycle' => $service->billing_cycle,
            'billing_cycle_label' => $service->billing_cycle
                ? $this->billingCycleLabel($service->billing_cycle)
                : null,
            'price' => (float) $service->price,
            'cost' => $service->cost !== null ? (float) $service->cost : null,
            'start_date' => $service->start_date?->format('Y-m-d'),
            'next_renewal_date' => $service->next_renewal_date?->format('Y-m-d'),
            'renewal_state' => $renewalPresentation['state'],
            'renewal_label' => $renewalPresentation['label'],
            'status' => $status,
            'status_label' => $statusPresentation['label'],
            'status_tone' => $statusPresentation['tone'],
            'notes' => $service->notes,
            'client' => [
                'id' => $service->client?->id,
                'name' => $service->client?->name,
                'company' => $service->client?->company,
                'display_name' => $service->client?->company
                    ?: $service->client?->name
                    ?: 'Sin cliente',
            ],
            'service_type' => [
                'id' => $service->serviceType?->id,
                'name' => $service->serviceType?->name,
            ],
            'provider' => $service->provider
                ? [
                    'id' => $service->provider->id,
                    'name' => $service->provider->name,
                ]
                : null,
            'created_at' => $service->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $service->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceDetailData(Service $service): array
    {
        $serviceData = $this->serviceData($service);

        return [
            ...$serviceData,
            'client' => [
                ...$serviceData['client'],
                'email' => $service->client?->email,
                'phone' => $service->client?->phone,
                'country' => $service->client?->country,
            ],
            'margin_amount' => (float) $service->price - (float) ($service->cost ?? 0),
            'margin_percentage' => $service->cost !== null && (float) $service->price > 0
                ? (((float) $service->price - (float) $service->cost) / (float) $service->price) * 100
                : null,
        ];
    }

    private function billingTypeLabel(string $value): string
    {
        return self::BILLING_TYPE_LABELS[$value] ?? ucfirst($value);
    }

    private function billingCycleLabel(string $value): string
    {
        return self::BILLING_CYCLE_LABELS[$value] ?? ucfirst($value);
    }

    /**
     * @return array{label: string, tone: string}
     */
    private function statusPresentation(string $status): array
    {
        return self::STATUS_PRESENTATIONS[$status] ?? [
            'label' => ucfirst($status),
            'tone' => 'pending',
        ];
    }

    /**
     * @return array{state: string, label: string}
     */
    private function renewalPresentation(?CarbonInterface $renewalDate): array
    {
        if ($renewalDate === null) {
            return [
                'state' => 'none',
                'label' => 'Sin fecha',
            ];
        }

        $today = Carbon::today();

        if ($renewalDate->isBefore($today)) {
            return [
                'state' => 'expired',
                'label' => 'Vencido',
            ];
        }

        if ($renewalDate->lessThanOrEqualTo($today->copy()->addDays(30))) {
            return [
                'state' => 'expiring',
                'label' => 'Por vencer',
            ];
        }

        return [
            'state' => 'scheduled',
            'label' => 'Programado',
        ];
    }
}
