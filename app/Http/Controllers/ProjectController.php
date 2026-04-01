<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $view = $this->normalizeView($request->string('view')->trim()->toString());

        $baseQuery = Project::query()
            ->when($search, function (Builder $query, string $search) {
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('client', function (Builder $clientQuery) use ($search) {
                            $clientQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('company', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        $projects = (clone $baseQuery)
            ->when($view !== 'all', function (Builder $query) use ($view) {
                $query->whereIn('status', $this->statusesByView($view));
            })
            ->with(['client:id,name,company'])
            ->withSum('payments as paid_amount', 'amount')
            ->latest()
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

        return to_route('projects.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): Response
    {
        $project->loadMissing(['client:id,name,company']);
        $project->loadSum('payments as paid_amount', 'amount');

        return Inertia::render('projects/Show', [
            'project' => $this->projectData($project),
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

        return to_route('projects.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return to_route('projects.index');
    }

    /**
     * @return array<int, array{key: string, label: string, count: int}>
     */
    private function viewOptions(Builder $baseQuery): array
    {
        $views = [
            'all' => 'All Projects',
            'planning' => 'Planning',
            'in_progress' => 'In Progress',
            'in_review' => 'In Review',
            'done' => 'Done',
        ];

        return collect($views)
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
        $map = [
            'planning' => [ProjectStatus::Draft->value],
            'in_progress' => [ProjectStatus::Active->value],
            'in_review' => [ProjectStatus::Paused->value],
            'done' => [ProjectStatus::Completed->value, ProjectStatus::Canceled->value],
        ];

        return $map[$view] ?? [];
    }

    private function normalizeView(string $view): string
    {
        $allowed = ['all', 'planning', 'in_progress', 'in_review', 'done'];

        return in_array($view, $allowed, true) ? $view : 'all';
    }

    /**
     * @return array<int, array{id: int, name: string, company: string, label: string}>
     */
    private function clientOptions(): array
    {
        return Client::query()
            ->orderBy('company')
            ->get(['id', 'name', 'company'])
            ->map(fn (Client $client) => [
                'id' => $client->id,
                'name' => $client->name,
                'company' => $client->company,
                'label' => $client->company ?: $client->name,
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
                'label' => $this->statusMeta($status->value)['label'],
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
        $progress = $this->statusProgress($status);
        $statusMeta = $this->statusMeta($status);
        $totalAmount = (float) $project->price;
        $paidAmount = (float) ($project->paid_amount ?? 0);
        $balanceDue = max($totalAmount - $paidAmount, 0);

        return [
            'id' => $project->id,
            'project_code' => sprintf('PRJ-%04d', $project->id),
            'name' => $project->name,
            'description' => $project->description,
            'status' => $status,
            'status_label' => $statusMeta['label'],
            'status_tone' => $statusMeta['tone'],
            'progress_percent' => $progress['percent'],
            'progress_label' => $progress['label'],
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
                'display_name' => $project->client?->company ?: $project->client?->name ?: 'No customer',
            ],
            'created_at' => $project->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $project->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array{label: string, tone: string}
     */
    private function statusMeta(string $status): array
    {
        return match ($status) {
            ProjectStatus::Draft->value => ['label' => 'Planning', 'tone' => 'planning'],
            ProjectStatus::Active->value => ['label' => 'In Progress', 'tone' => 'in_progress'],
            ProjectStatus::Paused->value => ['label' => 'In Review', 'tone' => 'in_review'],
            ProjectStatus::Completed->value => ['label' => 'Done', 'tone' => 'done'],
            ProjectStatus::Canceled->value => ['label' => 'Canceled', 'tone' => 'canceled'],
            default => ['label' => ucfirst($status), 'tone' => 'planning'],
        };
    }

    /**
     * @return array{percent: int, label: string}
     */
    private function statusProgress(string $status): array
    {
        return match ($status) {
            ProjectStatus::Draft->value => ['percent' => 15, 'label' => 'Kickoff stage'],
            ProjectStatus::Active->value => ['percent' => 65, 'label' => 'Stage 3 of 5'],
            ProjectStatus::Paused->value => ['percent' => 90, 'label' => 'Final review'],
            ProjectStatus::Completed->value => ['percent' => 100, 'label' => 'Completed'],
            ProjectStatus::Canceled->value => ['percent' => 0, 'label' => 'Canceled'],
            default => ['percent' => 10, 'label' => 'Initial planning'],
        };
    }
}
