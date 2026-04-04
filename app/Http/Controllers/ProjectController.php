<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectPayment;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    private const VIEW_LABELS = [
        'all' => 'Todos',
        'planning' => 'Planeacion',
        'in_progress' => 'En curso',
        'in_review' => 'En revision',
        'done' => 'Finalizados',
    ];

    private const VIEW_STATUS_MAP = [
        'planning' => ['draft'],
        'in_progress' => ['active'],
        'in_review' => ['paused'],
        'done' => ['completed', 'canceled'],
    ];

    private const STATUS_PRESENTATIONS = [
        'draft' => [
            'label' => 'Planeacion',
            'tone' => 'planning',
            'progress_percent' => 15,
            'progress_label' => 'Etapa de arranque',
        ],
        'active' => [
            'label' => 'En curso',
            'tone' => 'in_progress',
            'progress_percent' => 65,
            'progress_label' => 'Ejecucion en marcha',
        ],
        'paused' => [
            'label' => 'En revision',
            'tone' => 'in_review',
            'progress_percent' => 90,
            'progress_label' => 'Pendiente de revision final',
        ],
        'completed' => [
            'label' => 'Finalizado',
            'tone' => 'done',
            'progress_percent' => 100,
            'progress_label' => 'Proyecto finalizado',
        ],
        'canceled' => [
            'label' => 'Cancelado',
            'tone' => 'canceled',
            'progress_percent' => 0,
            'progress_label' => 'Proyecto cancelado',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $view = $this->normalizeView($request->string('view')->trim()->toString());

        $baseQuery = Project::query()
            ->when($search !== '', fn (Builder $query) => $query->search($search));

        $projects = (clone $baseQuery)
            ->when($view !== 'all', function (Builder $query) use ($view) {
                $query->whereIn('status', $this->statusesByView($view));
            })
            ->with(['client:id,name,company'])
            ->withSum('payments as paid_amount', 'amount')
            ->latest('created_at')
            ->paginate(8)
            ->withQueryString()
            ->through(fn (Project $project) => $this->projectData($project));

        return Inertia::render('projects/Index', [
            'projects' => $projects,
            'filters' => [
                'search' => $search,
                'view' => $view,
            ],
            'overview' => [
                'total_value' => (float) (clone $baseQuery)->sum('price'),
                'in_review' => (clone $baseQuery)
                    ->whereIn('status', $this->statusesByView('in_review'))
                    ->count(),
                'due_today' => (clone $baseQuery)
                    ->whereDate('due_date', now()->toDateString())
                    ->count(),
            ],
            'views' => $this->viewOptions($baseQuery),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('projects/Create', [
            'clients' => $this->clientOptions(),
            'status_options' => $this->statusOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return to_route('projects.index')->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): Response
    {
        $project->load([
            'client:id,name,company,email,phone,country',
            'payments' => fn ($query) => $query
                ->orderByDesc('payment_date')
                ->orderByDesc('id'),
            'modules' => fn ($query) => $query
                ->orderBy('order')
                ->orderBy('id'),
            'tasks' => fn ($query) => $query
                ->with('module:id,project_id,name,order')
                ->orderBy('order')
                ->orderBy('id'),
        ]);
        $project->loadSum('payments as paid_amount', 'amount');

        return Inertia::render('projects/Show', [
            'project' => $this->projectShowData($project),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): Response
    {
        $project->loadMissing(['client:id,name,company']);
        $project->loadSum('payments as paid_amount', 'amount');

        return Inertia::render('projects/Edit', [
            'project' => $this->projectData($project),
            'clients' => $this->clientOptions(),
            'status_options' => $this->statusOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return to_route('projects.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return to_route('projects.index')->with('success', 'Proyecto eliminado correctamente.');
    }

    /**
     * @return array<int, array{key: string, label: string, count: int}>
     */
    private function viewOptions(Builder $baseQuery): array
    {
        return collect(self::VIEW_LABELS)
            ->map(function (string $label, string $key) use ($baseQuery) {
                $count = $key === 'all'
                    ? (clone $baseQuery)->count()
                    : (clone $baseQuery)
                        ->whereIn('status', $this->statusesByView($key))
                        ->count();

                return [
                    'key' => $key,
                    'label' => $label,
                    'count' => $count,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function statusesByView(string $view): array
    {
        return self::VIEW_STATUS_MAP[$view] ?? [];
    }

    private function normalizeView(string $view): string
    {
        return array_key_exists($view, self::VIEW_LABELS) ? $view : 'all';
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
                    ? "{$client->company} · {$client->name}"
                    : $client->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return collect(ProjectStatus::cases())
            ->map(fn (ProjectStatus $status) => [
                'value' => $status->value,
                'label' => $this->statusPresentation($status->value)['label'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{
     *     id: int,
     *     project_code: string,
     *     name: string,
     *     description: string,
     *     status: string,
     *     status_label: string,
     *     status_tone: string,
     *     progress_percent: int,
     *     progress_label: string,
     *     total_amount: float,
     *     paid_amount: float,
     *     balance_due: float,
     *     start_date: string|null,
     *     due_date: string|null,
     *     is_due_today: bool,
     *     notes: string|null,
     *     client: array{id: int|null, name: string|null, company: string|null, display_name: string},
     *     created_at: string|null,
     *     updated_at: string|null
     * }
     */
    private function projectData(Project $project): array
    {
        $status = strtolower((string) $project->status);
        $presentation = $this->statusPresentation($status);
        $totalAmount = (float) $project->price;
        $paidAmount = (float) ($project->paid_amount ?? 0);
        $balanceDue = max($totalAmount - $paidAmount, 0);

        return [
            'id' => $project->id,
            'project_code' => sprintf('PRJ-%04d', $project->id),
            'name' => $project->name,
            'description' => $project->description,
            'status' => $status,
            'status_label' => $presentation['label'],
            'status_tone' => $presentation['tone'],
            'progress_percent' => $presentation['progress_percent'],
            'progress_label' => $presentation['progress_label'],
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_due' => $balanceDue,
            'start_date' => $project->start_date?->format('Y-m-d'),
            'due_date' => $project->due_date?->format('Y-m-d'),
            'is_due_today' => $project->due_date?->isToday() ?? false,
            'notes' => $project->notes,
            'client' => [
                'id' => $project->client?->id,
                'name' => $project->client?->name,
                'company' => $project->client?->company,
                'display_name' => $project->client?->company ?: $project->client?->name ?: 'Sin cliente',
            ],
            'created_at' => $project->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $project->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function projectShowData(Project $project): array
    {
        $projectData = $this->projectData($project);

        return [
            ...$projectData,
            'client' => [
                ...$projectData['client'],
                'email' => $project->client?->email,
                'phone' => $project->client?->phone,
                'country' => $project->client?->country,
            ],
            'financial_summary' => [
                'total_price' => (float) $project->price,
                'total_paid' => (float) ($project->paid_amount ?? 0),
                'pending_balance' => max(
                    (float) $project->price - (float) ($project->paid_amount ?? 0),
                    0
                ),
            ],
            'payments' => $project->payments
                ->map(fn (ProjectPayment $payment) => $this->projectPaymentData($payment))
                ->values()
                ->all(),
            'modules' => $project->modules
                ->map(fn (ProjectModule $module) => $this->projectModuleData($module))
                ->values()
                ->all(),
            'tasks' => $project->tasks
                ->map(fn (Task $task) => $this->projectTaskData($task))
                ->values()
                ->all(),
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
     *     updated_at: string|null
     * }
     */
    private function projectPaymentData(ProjectPayment $payment): array
    {
        return [
            'id' => $payment->id,
            'amount' => (float) $payment->amount,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'payment_method' => (string) $payment->payment_method,
            'notes' => $payment->notes,
            'created_at' => $payment->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $payment->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     order: int
     * }
     */
    private function projectModuleData(ProjectModule $module): array
    {
        return [
            'id' => $module->id,
            'name' => $module->name,
            'order' => (int) $module->order,
        ];
    }

    /**
     * @return array{
     *     id: int,
     *     module_id: int|null,
     *     title: string,
     *     description: string|null,
     *     status: string,
     *     priority: string|null,
     *     order: int,
     *     module: array{id: int, name: string, order: int}|null,
     *     created_at: string|null,
     *     updated_at: string|null
     * }
     */
    private function projectTaskData(Task $task): array
    {
        return [
            'id' => $task->id,
            'module_id' => $task->module_id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => (string) $task->status,
            'priority' => $task->priority,
            'order' => (int) $task->order,
            'module' => $task->module
                ? [
                    'id' => $task->module->id,
                    'name' => $task->module->name,
                    'order' => (int) $task->module->order,
                ]
                : null,
            'created_at' => $task->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $task->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array{
     *     label: string,
     *     tone: string,
     *     progress_percent: int,
     *     progress_label: string
     * }
     */
    private function statusPresentation(string $status): array
    {
        return self::STATUS_PRESENTATIONS[$status] ?? [
            'label' => ucfirst($status),
            'tone' => 'planning',
            'progress_percent' => 10,
            'progress_label' => 'Planificacion inicial',
        ];
    }
}
