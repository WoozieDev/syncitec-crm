import type {
    PersonalTask,
    PersonalTaskPriorityOption,
    PersonalTaskStatusOption,
} from './personalTask';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedPersonalTasks {
    data: PersonalTask[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface PersonalTaskOverview {
    total_tasks: number;
    pending: number;
    in_progress: number;
    completed: number;
}

export interface PersonalTaskIndexProps {
    tasks: PaginatedPersonalTasks;
    filters: {
        search: string;
        status: string;
        priority: string;
    };
    overview: PersonalTaskOverview;
    status_options: PersonalTaskStatusOption[];
    priority_options: PersonalTaskPriorityOption[];
}

export interface PersonalTaskCreateProps {
    status_options: PersonalTaskStatusOption[];
    priority_options: PersonalTaskPriorityOption[];
}

export interface PersonalTaskEditProps extends PersonalTaskCreateProps {
    task: PersonalTask;
}

export interface PersonalTaskShowProps {
    task: PersonalTask;
}
