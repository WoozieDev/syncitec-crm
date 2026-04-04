<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectPayments\StoreProjectPaymentRequest;
use App\Http\Requests\ProjectPayments\UpdateProjectPaymentRequest;
use App\Models\Project;
use App\Models\ProjectPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $projectId = $request->integer('project_id');
        $paymentMethod = $request->string('payment_method')->trim()->toString();

        $baseQuery = ProjectPayment::query()
            ->with([
                'project:id,client_id,name,status,price',
                'project.client:id,name,company',
            ])
            ->when($search !== '', fn (Builder $query) => $query->search($search))
            ->when($projectId > 0, fn (Builder $query) => $query->where('project_id', $projectId))
            ->when(
                $paymentMethod !== '',
                fn (Builder $query) => $query->where('payment_method', 'like', "%{$paymentMethod}%")
            );

        $totalPayments = (clone $baseQuery)->count();
        $totalPaid = (float) (clone $baseQuery)->sum('amount');

        $payments = (clone $baseQuery)
            ->latest('payment_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ProjectPayment $payment) => $this->paymentListData($payment));

        return Inertia::render('projectPayments/Index', [
            'payments' => $payments,
            'filters' => [
                'search' => $search,
                'project_id' => $projectId > 0 ? (string) $projectId : '',
                'payment_method' => $paymentMethod,
            ],
            'overview' => [
                'total_payments' => $totalPayments,
                'total_paid' => $totalPaid,
                'unique_projects' => (clone $baseQuery)->distinct('project_id')->count('project_id'),
                'average_payment' => $totalPayments > 0 ? $totalPaid / $totalPayments : 0,
            ],
            'projects' => $this->projectOptions(),
            'payment_methods' => ProjectPayment::query()
                ->whereNotNull('payment_method')
                ->where('payment_method', '!=', '')
                ->orderBy('payment_method')
                ->distinct()
                ->pluck('payment_method')
                ->values()
                ->all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        $selectedProjectId = $request->integer('project');

        return Inertia::render('projectPayments/Create', [
            'projects' => $this->projectOptions(),
            'selected_project_id' => $selectedProjectId > 0 ? $selectedProjectId : null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectPaymentRequest $request): RedirectResponse
    {
        ProjectPayment::create($request->validated());

        return to_route('project-payments.index')->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectPayment $projectPayment): Response
    {
        $projectPayment->load([
            'project:id,client_id,name,status,price',
            'project.client:id,name,company,email,phone,country',
        ]);

        if ($projectPayment->project) {
            $projectPayment->project->loadSum('payments as paid_amount', 'amount');
        }

        return Inertia::render('projectPayments/Show', [
            'payment' => $this->paymentDetailData($projectPayment),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectPayment $projectPayment): Response
    {
        $projectPayment->load([
            'project:id,client_id,name,status,price',
            'project.client:id,name,company,email,phone,country',
        ]);

        if ($projectPayment->project) {
            $projectPayment->project->loadSum('payments as paid_amount', 'amount');
        }

        return Inertia::render('projectPayments/Edit', [
            'payment' => $this->paymentDetailData($projectPayment),
            'projects' => $this->projectOptions(),
            'selected_project_id' => $projectPayment->project_id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectPaymentRequest $request, ProjectPayment $projectPayment): RedirectResponse
    {
        $projectPayment->update($request->validated());

        return to_route('project-payments.index')->with('success', 'Pago actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectPayment $projectPayment): RedirectResponse
    {
        $projectPayment->delete();

        return to_route('project-payments.index')->with('success', 'Pago eliminado correctamente.');
    }

    /**
     * @return array<int, array{
     *     id: int,
     *     name: string,
     *     client_name: string,
     *     client_display_name: string,
     *     status: string,
     *     total_amount: float,
     *     paid_amount: float,
     *     balance_due: float,
     *     label: string
     * }>
     */
    private function projectOptions(): array
    {
        return Project::query()
            ->with(['client:id,name,company'])
            ->withSum('payments as paid_amount', 'amount')
            ->orderBy('name')
            ->get(['id', 'client_id', 'name', 'status', 'price'])
            ->map(function (Project $project) {
                $clientDisplayName = $project->client?->company ?: $project->client?->name ?: 'Sin cliente';
                $paidAmount = (float) ($project->paid_amount ?? 0);
                $totalAmount = (float) $project->price;

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client_name' => $project->client?->name ?: 'Sin cliente',
                    'client_display_name' => $clientDisplayName,
                    'status' => (string) $project->status,
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'balance_due' => max($totalAmount - $paidAmount, 0),
                    'label' => "{$project->name} - {$clientDisplayName}",
                ];
            })
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
     *     project: array{
     *         id: int,
     *         name: string,
     *         status: string,
     *         total_amount: float,
     *         paid_amount: float,
     *         balance_due: float,
     *         client: array{
     *             id: int|null,
     *             name: string|null,
     *             company: string|null,
     *             email: string|null,
     *             phone: string|null,
     *             country: string|null,
     *             display_name: string
     *         }
     *     }|null
     * }
     */
    private function paymentDetailData(ProjectPayment $payment): array
    {
        $project = $payment->project;
        $projectTotal = (float) ($project?->price ?? 0);
        $projectPaid = (float) ($project?->paid_amount ?? 0);

        return [
            'id' => $payment->id,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => (string) $payment->payment_method,
            'notes' => $payment->notes,
            'created_at' => $payment->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $payment->updated_at?->format('Y-m-d H:i:s'),
            'project' => $project
                ? [
                    'id' => $project->id,
                    'name' => $project->name,
                    'status' => (string) $project->status,
                    'total_amount' => $projectTotal,
                    'paid_amount' => $projectPaid,
                    'balance_due' => max($projectTotal - $projectPaid, 0),
                    'client' => [
                        'id' => $project->client?->id,
                        'name' => $project->client?->name,
                        'company' => $project->client?->company,
                        'email' => $project->client?->email,
                        'phone' => $project->client?->phone,
                        'country' => $project->client?->country,
                        'display_name' => $project->client?->company
                            ?: $project->client?->name
                            ?: 'Sin cliente',
                    ],
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
     *     project: array{
     *         id: int,
     *         name: string,
     *         client: array{
     *             id: int|null,
     *             name: string|null,
     *             company: string|null,
     *             display_name: string
     *         }
     *     }|null
     * }
     */
    private function paymentListData(ProjectPayment $payment): array
    {
        $project = $payment->project;

        return [
            'id' => $payment->id,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => (string) $payment->payment_method,
            'notes' => $payment->notes,
            'created_at' => $payment->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $payment->updated_at?->format('Y-m-d H:i:s'),
            'project' => $project
                ? [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client' => [
                        'id' => $project->client?->id,
                        'name' => $project->client?->name,
                        'company' => $project->client?->company,
                        'display_name' => $project->client?->company
                            ?: $project->client?->name
                            ?: 'Sin cliente',
                    ],
                ]
                : null,
        ];
    }
}
