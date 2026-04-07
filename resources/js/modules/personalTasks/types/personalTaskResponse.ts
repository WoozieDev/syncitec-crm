import type {
    PersonalTask,
    PersonalTaskGroup,
    PersonalTaskPriorityOption,
} from './personalTask';

export interface PersonalTaskOverview {
    total_tasks: number;
    open_tasks: number;
    completed: number;
    overdue: number;
    today: number;
    this_week: number;
    next_week: number;
    backlog: number;
}

export interface PersonalTaskIndexProps {
    filters: {
        search: string;
        priority: string;
        completion: string;
    };
    overview: PersonalTaskOverview;
    priority_options: PersonalTaskPriorityOption[];
    grouped_tasks: PersonalTaskGroup[];
}

export interface PersonalTaskCreateProps {
    priority_options: PersonalTaskPriorityOption[];
}

export interface PersonalTaskEditProps extends PersonalTaskCreateProps {
    task: PersonalTask;
}

export interface PersonalTaskShowProps {
    task: PersonalTask;
}
