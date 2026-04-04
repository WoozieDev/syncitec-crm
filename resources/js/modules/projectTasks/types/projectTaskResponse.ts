import type {
    ProjectTask,
    ProjectTaskPriorityOption,
    ProjectTaskProjectOption,
    ProjectTaskStatusOption,
} from './projectTask';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedProjectTasks {
    data: ProjectTask[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface ProjectTaskOverview {
    total_tasks: number;
    pending: number;
    in_progress: number;
    completed: number;
}

export interface ProjectTaskIndexProps {
    tasks: PaginatedProjectTasks;
    filters: {
        search: string;
        project_id: string;
        status: string;
        priority: string;
    };
    overview: ProjectTaskOverview;
    projects: ProjectTaskProjectOption[];
    status_options: ProjectTaskStatusOption[];
    priority_options: ProjectTaskPriorityOption[];
}

export interface ProjectTaskCreateProps {
    projects: ProjectTaskProjectOption[];
    selected_project_id: number | null;
    status_options: ProjectTaskStatusOption[];
    priority_options: ProjectTaskPriorityOption[];
}

export interface ProjectTaskEditProps extends ProjectTaskCreateProps {
    task: ProjectTask;
}

export interface ProjectTaskShowProps {
    task: ProjectTask;
}
