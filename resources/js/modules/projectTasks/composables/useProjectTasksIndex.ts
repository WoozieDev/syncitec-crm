import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PaginatedProjectTasks,
    ProjectTask,
    ProjectTaskIndexProps,
    ProjectTaskOverview,
    ProjectTaskPriorityOption,
    ProjectTaskProjectOption,
    ProjectTaskStatusOption,
} from '@/modules/projectTasks/types';
import { destroy, index } from '@/routes/project-tasks';

export type UseProjectTasksIndexReturn = {
    tasks: ComputedRef<PaginatedProjectTasks>;
    overview: ComputedRef<ProjectTaskOverview>;
    projects: ComputedRef<ProjectTaskProjectOption[]>;
    statusOptions: ComputedRef<ProjectTaskStatusOption[]>;
    priorityOptions: ComputedRef<ProjectTaskPriorityOption[]>;
    search: Ref<string>;
    projectId: Ref<string>;
    status: Ref<string>;
    priority: Ref<string>;
    totalTasks: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (task: ProjectTask) => void;
};

export const useProjectTasksIndex = (
    props: ProjectTaskIndexProps,
): UseProjectTasksIndexReturn => {
    const tasks = computed(() => props.tasks);
    const overview = computed(() => props.overview);
    const projects = computed(() => props.projects);
    const statusOptions = computed(() => props.status_options);
    const priorityOptions = computed(() => props.priority_options);

    const search = ref(props.filters.search ?? '');
    const projectId = ref(props.filters.project_id ?? '');
    const status = ref(props.filters.status ?? '');
    const priority = ref(props.filters.priority ?? '');

    const applyFilters = () => {
        router.get(
            index(),
            {
                search: search.value || undefined,
                project_id: projectId.value || undefined,
                status: status.value || undefined,
                priority: priority.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    watch([projectId, status, priority], () => {
        applyFilters();
    });

    watchDebounced(
        search,
        () => {
            applyFilters();
        },
        { debounce: 300, maxWait: 900 },
    );

    const hasTasks = computed(() => tasks.value.data.length > 0);
    const totalTasks = computed(() => tasks.value.total ?? tasks.value.data.length);
    const pageFrom = computed(() => tasks.value.from ?? (hasTasks.value ? 1 : 0));
    const pageTo = computed(() => tasks.value.to ?? tasks.value.data.length);

    const handleDelete = (task: ProjectTask) => {
        if (!confirm(`Seguro que deseas eliminar la tarea "${task.title}"?`)) {
            return;
        }

        router.delete(destroy(task.id), {
            preserveScroll: true,
        });
    };

    return {
        tasks,
        overview,
        projects,
        statusOptions,
        priorityOptions,
        search,
        projectId,
        status,
        priority,
        totalTasks,
        pageFrom,
        pageTo,
        handleDelete,
    };
};
