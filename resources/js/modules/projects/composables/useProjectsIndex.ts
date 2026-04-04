import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PaginatedProjects,
    Project,
    ProjectIndexProps,
    ProjectOverview,
    ProjectViewFilter,
} from '@/modules/projects/types';
import { destroy, index } from '@/routes/projects';

export type UseProjectsIndexReturn = {
    projects: ComputedRef<PaginatedProjects>;
    overview: ComputedRef<ProjectOverview>;
    views: ComputedRef<ProjectViewFilter[]>;
    search: Ref<string>;
    currentView: Ref<string>;
    totalProjects: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (project: Project) => void;
};

export const useProjectsIndex = (
    props: ProjectIndexProps,
): UseProjectsIndexReturn => {
    const projects = computed(() => props.projects);
    const overview = computed(() => props.overview);
    const views = computed(() => props.views);
    const search = ref(props.filters.search ?? '');
    const currentView = ref(props.filters.view ?? 'all');

    const applyFilters = () => {
        router.get(
            index(),
            {
                search: search.value || undefined,
                view:
                    currentView.value !== 'all'
                        ? currentView.value
                        : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    watch(currentView, () => {
        applyFilters();
    });

    watchDebounced(
        search,
        () => {
            applyFilters();
        },
        { debounce: 300, maxWait: 900 },
    );

    const hasProjects = computed(() => projects.value.data.length > 0);
    const totalProjects = computed(
        () => projects.value.total ?? projects.value.data.length,
    );
    const pageFrom = computed(
        () => projects.value.from ?? (hasProjects.value ? 1 : 0),
    );
    const pageTo = computed(
        () => projects.value.to ?? projects.value.data.length,
    );

    const handleDelete = (project: Project) => {
        if (!confirm(`Seguro que deseas eliminar "${project.name}"?`)) {
            return;
        }

        router.delete(destroy(project.id), {
            preserveScroll: true,
        });
    };

    return {
        projects,
        overview,
        views,
        search,
        currentView,
        totalProjects,
        pageFrom,
        pageTo,
        handleDelete,
    };
};
