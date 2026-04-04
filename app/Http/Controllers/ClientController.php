<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\Service;
use App\Models\ServicePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    private const PROJECT_STATUS_PRESENTATIONS = [
        'draft' => [
            'label' => 'Planeacion',
            'tone' => 'planning',
        ],
        'active' => [
            'label' => 'En curso',
            'tone' => 'in_progress',
        ],
        'paused' => [
            'label' => 'En revision',
            'tone' => 'in_review',
        ],
        'completed' => [
            'label' => 'Finalizado',
            'tone' => 'done',
        ],
        'canceled' => [
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

        $clients = Client::query()
            ->when($search, fn ($query) => $query->search($search))
            ->latest('created_at')
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

        return to_route('clients.index')->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): Response
    {
        $client->load([
            'projects' => fn ($query) => $query
                ->withSum('payments as paid_amount', 'amount')
                ->with('payments:id,project_id,amount,payment_date,payment_method,notes')
                ->latest('created_at'),
            'services' => fn ($query) => $query
                ->with([
                    'serviceType:id,name',
                    'provider:id,name',
                    'payments:id,service_id,amount,payment_date,payment_method,notes',
                ])
                ->withSum('payments as paid_amount', 'amount')
                ->latest('created_at'),
        ]);

        return Inertia::render('clients/Show', [
            'client' => $this->clientShowData($client),
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

        return to_route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return to_route('clients.index')->with('success', 'Client deleted successfully.');
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

    /**
     * Normalizes the data exposed to the client detail view.
     *
     * @return array<string, mixed>
     */
    private function clientShowData(Client $client): array
    {
        $recurringServices = $client->services
            ->filter(fn (Service $service) => $service->billing_type === 'recurrente')
            ->values();

        return [
            ...$this->clientData($client),
            'financial_summary' => $this->financialSummaryData($client),
            'projects' => $client->projects
                ->map(fn (Project $project) => $this->projectSummaryData($project))
                ->values()
                ->all(),
            'services' => $recurringServices
                ->map(fn (Service $service) => $this->serviceSummaryData($service))
                ->all(),
            'recent_payments' => $this->recentPaymentsData($client),
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     project_code: string,
     *     name: string,
     *     status: string,
     *     status_label: string,
     *     status_tone: string,
     *     total_amount: float,
     *     paid_amount: float,
     *     balance_due: float,
     *     start_date: string|null,
     *     due_date: string|null,
     *     created_at: string|null
     * }
     */
    private function projectSummaryData(Project $project): array
    {
        $status = strtolower((string) $project->status);
        $presentation = $this->projectStatusPresentation($status);

        return [
            'id' => $project->id,
            'project_code' => sprintf('PRJ-%04d', $project->id),
            'name' => $project->name,
            'status' => $status,
            'status_label' => $presentation['label'],
            'status_tone' => $presentation['tone'],
            'total_amount' => (float) $project->price,
            'paid_amount' => (float) ($project->paid_amount ?? 0),
            'balance_due' => max((float) $project->price - (float) ($project->paid_amount ?? 0), 0),
            'start_date' => $project->start_date?->format('Y-m-d'),
            'due_date' => $project->due_date?->format('Y-m-d'),
            'created_at' => $project->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array{
     *     total_received: float,
     *     project_value: float,
     *     project_paid: float,
     *     project_balance: float,
     *     service_value: float,
     *     service_paid: float,
     *     payments_count: int,
     *     recurring_services_count: int,
     *     recurring_services_value: float
     * }
     */
    private function financialSummaryData(Client $client): array
    {
        $projectValue = (float) $client->projects->sum(fn (Project $project) => (float) $project->price);
        $projectPaid = (float) $client->projects->sum(fn (Project $project) => (float) ($project->paid_amount ?? 0));
        $projectBalance = max($projectValue - $projectPaid, 0);
        $serviceValue = (float) $client->services->sum(fn (Service $service) => (float) $service->price);
        $servicePaid = (float) $client->services->sum(fn (Service $service) => (float) ($service->paid_amount ?? 0));
        $recurringServices = $client->services->filter(fn (Service $service) => $service->billing_type === 'recurrente');

        return [
            'total_received' => $projectPaid + $servicePaid,
            'project_value' => $projectValue,
            'project_paid' => $projectPaid,
            'project_balance' => $projectBalance,
            'service_value' => $serviceValue,
            'service_paid' => $servicePaid,
            'payments_count' => $client->projects->sum(fn (Project $project) => $project->payments->count())
                + $client->services->sum(fn (Service $service) => $service->payments->count()),
            'recurring_services_count' => $recurringServices->count(),
            'recurring_services_value' => (float) $recurringServices->sum(fn (Service $service) => (float) $service->price),
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     service_type: string|null,
     *     provider: string|null,
     *     billing_type: string,
     *     billing_cycle: string|null,
     *     price: float,
     *     paid_amount: float,
     *     status: string,
     *     start_date: string|null,
     *     next_renewal_date: string|null,
     *     notes: string|null
     * }
     */
    private function serviceSummaryData(Service $service): array
    {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'service_type' => $service->serviceType?->name,
            'provider' => $service->provider?->name,
            'billing_type' => (string) $service->billing_type,
            'billing_cycle' => $service->billing_cycle,
            'price' => (float) $service->price,
            'paid_amount' => (float) ($service->paid_amount ?? 0),
            'status' => (string) $service->status,
            'start_date' => $service->start_date?->format('Y-m-d'),
            'next_renewal_date' => $service->next_renewal_date?->format('Y-m-d'),
            'notes' => $service->notes,
        ];
    }

    /**
     * @return array<int, array{
     *     id: string,
     *     source_type: string,
     *     source_label: string,
     *     source_name: string,
     *     amount: float,
     *     payment_date: string|null,
     *     payment_method: string,
     *     notes: string|null
     * }>
     */
    private function recentPaymentsData(Client $client): array
    {
        $projectPayments = $client->projects
            ->flatMap(fn (Project $project) => $project->payments->map(
                fn (ProjectPayment $payment) => $this->projectPaymentData($project, $payment)
            ));

        $servicePayments = $client->services
            ->flatMap(fn (Service $service) => $service->payments->map(
                fn (ServicePayment $payment) => $this->servicePaymentData($service, $payment)
            ));

        return $projectPayments
            ->concat($servicePayments)
            ->sortByDesc(fn (array $payment) => $payment['payment_date'] ?? '')
            ->take(6)
            ->values()
            ->all();
    }

    /**
     * @return array{
     *     id: string,
     *     source_type: string,
     *     source_label: string,
     *     source_name: string,
     *     amount: float,
     *     payment_date: string|null,
     *     payment_method: string,
     *     notes: string|null
     * }
     */
    private function projectPaymentData(Project $project, ProjectPayment $payment): array
    {
        return [
            'id' => 'project-'.$payment->id,
            'source_type' => 'project',
            'source_label' => 'Proyecto',
            'source_name' => $project->name,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => (string) $payment->payment_method,
            'notes' => $payment->notes,
        ];
    }

    /**
     * @return array{
     *     id: string,
     *     source_type: string,
     *     source_label: string,
     *     source_name: string,
     *     amount: float,
     *     payment_date: string|null,
     *     payment_method: string,
     *     notes: string|null
     * }
     */
    private function servicePaymentData(Service $service, ServicePayment $payment): array
    {
        return [
            'id' => 'service-'.$payment->id,
            'source_type' => 'service',
            'source_label' => 'Servicio',
            'source_name' => $service->name,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => (string) $payment->payment_method,
            'notes' => $payment->notes,
        ];
    }

    /**
     * @return array{label: string, tone: string}
     */
    private function projectStatusPresentation(string $status): array
    {
        return self::PROJECT_STATUS_PRESENTATIONS[$status] ?? [
            'label' => ucfirst($status),
            'tone' => 'planning',
        ];
    }
}
