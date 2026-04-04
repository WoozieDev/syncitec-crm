<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicePayments\StoreServicePaymentRequest;
use App\Http\Requests\ServicePayments\UpdateServicePaymentRequest;
use App\Models\Service;
use App\Models\ServicePayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServicePaymentController extends Controller
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

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $serviceId = $request->integer('service_id');
        $paymentMethod = $request->string('payment_method')->trim()->toString();
        $providerId = $request->integer('provider_id');

        $baseQuery = ServicePayment::query()
            ->with([
                'service:id,client_id,service_type_id,provider_id,name,status,price,next_renewal_date',
                'service.client:id,name,company',
                'service.serviceType:id,name',
                'service.provider:id,name',
            ])
            ->when($search !== '', fn (Builder $query) => $query->search($search))
            ->when($serviceId > 0, fn (Builder $query) => $query->where('service_id', $serviceId))
            ->when(
                $paymentMethod !== '',
                fn (Builder $query) => $query->where('payment_method', 'like', "%{$paymentMethod}%")
            )
            ->when(
                $providerId > 0,
                fn (Builder $query) => $query->whereHas(
                    'service',
                    fn (Builder $serviceQuery) => $serviceQuery->where('provider_id', $providerId)
                )
            );

        $totalPayments = (clone $baseQuery)->count();
        $totalPaid = (float) (clone $baseQuery)->sum('amount');

        $payments = (clone $baseQuery)
            ->latest('payment_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ServicePayment $payment) => $this->paymentListData($payment));

        return Inertia::render('servicePayments/Index', [
            'payments' => $payments,
            'filters' => [
                'search' => $search,
                'service_id' => $serviceId > 0 ? (string) $serviceId : '',
                'payment_method' => $paymentMethod,
                'provider_id' => $providerId > 0 ? (string) $providerId : '',
            ],
            'overview' => [
                'total_payments' => $totalPayments,
                'total_paid' => $totalPaid,
                'unique_services' => (clone $baseQuery)->distinct('service_id')->count('service_id'),
                'average_payment' => $totalPayments > 0 ? $totalPaid / $totalPayments : 0,
            ],
            'services' => $this->serviceOptions(),
            'providers' => $this->providerOptions(),
            'payment_methods' => ServicePayment::query()
                ->whereNotNull('payment_method')
                ->where('payment_method', '!=', '')
                ->orderBy('payment_method')
                ->distinct()
                ->pluck('payment_method')
                ->values()
                ->all(),
        ]);
    }

    public function create(Request $request): Response
    {
        $selectedServiceId = $request->integer('service');

        return Inertia::render('servicePayments/Create', [
            'services' => $this->serviceOptions(),
            'selected_service_id' => $selectedServiceId > 0 ? $selectedServiceId : null,
        ]);
    }

    public function store(StoreServicePaymentRequest $request): RedirectResponse
    {
        ServicePayment::create($request->validated());

        return to_route('service-payments.index')->with('success', 'Pago de servicio registrado correctamente.');
    }

    public function show(ServicePayment $servicePayment): Response
    {
        $servicePayment->load($this->serviceRelations(true));

        if ($servicePayment->service) {
            $servicePayment->service->loadSum('payments as paid_amount', 'amount');
        }

        return Inertia::render('servicePayments/Show', [
            'payment' => $this->paymentDetailData($servicePayment),
        ]);
    }

    public function edit(ServicePayment $servicePayment): Response
    {
        $servicePayment->load($this->serviceRelations(true));

        if ($servicePayment->service) {
            $servicePayment->service->loadSum('payments as paid_amount', 'amount');
        }

        return Inertia::render('servicePayments/Edit', [
            'payment' => $this->paymentDetailData($servicePayment),
            'services' => $this->serviceOptions(),
            'selected_service_id' => $servicePayment->service_id,
        ]);
    }

    public function update(
        UpdateServicePaymentRequest $request,
        ServicePayment $servicePayment,
    ): RedirectResponse {
        $servicePayment->update($request->validated());

        return to_route('service-payments.index')->with('success', 'Pago de servicio actualizado correctamente.');
    }

    public function destroy(ServicePayment $servicePayment): RedirectResponse
    {
        $servicePayment->delete();

        return to_route('service-payments.index')->with('success', 'Pago de servicio eliminado correctamente.');
    }

    /**
     * @return array<int, string>
     */
    private function serviceRelations(bool $withClientDetails = false): array
    {
        return [
            $withClientDetails
                ? 'service.client:id,name,company,email,phone,country'
                : 'service.client:id,name,company',
            'service.serviceType:id,name',
            'service.provider:id,name',
        ];
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     name: string,
     *     client: array{id: int|null, name: string|null, company: string|null, display_name: string},
     *     service_type: array{id: int|null, name: string|null},
     *     provider: array{id: int|null, name: string|null}|null,
     *     billing_type: string,
     *     billing_type_label: string,
     *     billing_cycle: string|null,
     *     billing_cycle_label: string|null,
     *     status: string,
     *     status_label: string,
     *     status_tone: string,
     *     price: float,
     *     paid_amount: float,
     *     balance_due: float,
     *     next_renewal_date: string|null,
     *     label: string
     * }>
     */
    private function serviceOptions(): array
    {
        return Service::query()
            ->with([
                'client:id,name,company',
                'serviceType:id,name',
                'provider:id,name',
            ])
            ->withSum('payments as paid_amount', 'amount')
            ->orderBy('name')
            ->get([
                'id',
                'client_id',
                'service_type_id',
                'provider_id',
                'name',
                'billing_type',
                'billing_cycle',
                'price',
                'status',
                'next_renewal_date',
            ])
            ->map(function (Service $service) {
                $statusPresentation = $this->statusPresentation((string) $service->status);
                $paidAmount = (float) ($service->paid_amount ?? 0);
                $price = (float) $service->price;

                return [
                    'id' => $service->id,
                    'name' => $service->name,
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
                    'billing_type' => (string) $service->billing_type,
                    'billing_type_label' => $this->billingTypeLabel((string) $service->billing_type),
                    'billing_cycle' => $service->billing_cycle,
                    'billing_cycle_label' => $service->billing_cycle
                        ? $this->billingCycleLabel($service->billing_cycle)
                        : null,
                    'status' => (string) $service->status,
                    'status_label' => $statusPresentation['label'],
                    'status_tone' => $statusPresentation['tone'],
                    'price' => $price,
                    'paid_amount' => $paidAmount,
                    'balance_due' => max($price - $paidAmount, 0),
                    'next_renewal_date' => $service->next_renewal_date?->format('Y-m-d'),
                    'label' => $service->client?->company
                        ? "{$service->name} - {$service->client->company}"
                        : $service->name,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string, label: string}>
     */
    private function providerOptions(): array
    {
        return Service::query()
            ->join('providers', 'providers.id', '=', 'services.provider_id')
            ->select('providers.id', 'providers.name')
            ->distinct()
            ->orderBy('providers.name')
            ->get()
            ->map(fn ($provider) => [
                'id' => (int) $provider->id,
                'name' => (string) $provider->name,
                'label' => (string) $provider->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     amount: float,
     *     payment_date: string|null,
     *     payment_method: string,
     *     notes: string|null,
     *     created_at: string|null,
     *     updated_at: string|null,
     *     service: array<string, mixed>|null
     * }
     */
    private function paymentDetailData(ServicePayment $payment): array
    {
        $service = $payment->service;
        $servicePrice = (float) ($service?->price ?? 0);
        $servicePaid = (float) ($service?->paid_amount ?? 0);
        $statusPresentation = $this->statusPresentation((string) ($service?->status ?? ''));

        return [
            'id' => $payment->id,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => (string) $payment->payment_method,
            'notes' => $payment->notes,
            'created_at' => $payment->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $payment->updated_at?->format('Y-m-d H:i:s'),
            'service' => $service
                ? [
                    'id' => $service->id,
                    'name' => $service->name,
                    'status' => (string) $service->status,
                    'status_label' => $statusPresentation['label'],
                    'status_tone' => $statusPresentation['tone'],
                    'price' => $servicePrice,
                    'paid_amount' => $servicePaid,
                    'balance_due' => max($servicePrice - $servicePaid, 0),
                    'next_renewal_date' => $service->next_renewal_date?->format('Y-m-d'),
                    'billing_type' => (string) $service->billing_type,
                    'billing_type_label' => $this->billingTypeLabel((string) $service->billing_type),
                    'billing_cycle' => $service->billing_cycle,
                    'billing_cycle_label' => $service->billing_cycle
                        ? $this->billingCycleLabel($service->billing_cycle)
                        : null,
                    'client' => [
                        'id' => $service->client?->id,
                        'name' => $service->client?->name,
                        'company' => $service->client?->company,
                        'email' => $service->client?->email,
                        'phone' => $service->client?->phone,
                        'country' => $service->client?->country,
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
                ]
                : null,
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     amount: float,
     *     payment_date: string|null,
     *     payment_method: string,
     *     notes: string|null,
     *     created_at: string|null,
     *     updated_at: string|null,
     *     service: array<string, mixed>|null
     * }
     */
    private function paymentListData(ServicePayment $payment): array
    {
        $service = $payment->service;
        $statusPresentation = $this->statusPresentation((string) ($service?->status ?? ''));

        return [
            'id' => $payment->id,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => (string) $payment->payment_method,
            'notes' => $payment->notes,
            'created_at' => $payment->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $payment->updated_at?->format('Y-m-d H:i:s'),
            'service' => $service
                ? [
                    'id' => $service->id,
                    'name' => $service->name,
                    'status' => (string) $service->status,
                    'status_label' => $statusPresentation['label'],
                    'status_tone' => $statusPresentation['tone'],
                    'price' => (float) $service->price,
                    'next_renewal_date' => $service->next_renewal_date?->format('Y-m-d'),
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
                ]
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
        return self::STATUS_PRESENTATIONS[strtolower($status)] ?? [
            'label' => $status !== '' ? ucfirst($status) : 'Sin estado',
            'tone' => 'pending',
        ];
    }
}
