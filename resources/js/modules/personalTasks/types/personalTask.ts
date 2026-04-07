export interface PersonalTaskStatusOption {
    value: string;
    label: string;
}

export interface PersonalTaskPriorityOption {
    value: string;
    label: string;
}

export interface PersonalTask {
    id: number;
    title: string;
    description: string | null;
    status: string;
    priority: string | null;
    due_date: string | null;
    completed_at: string | null;
    is_completed: boolean;
    order: number;
    created_at?: string | null;
    updated_at?: string | null;
}

export interface PersonalTaskGroup {
    key: string;
    label: string;
    count: number;
    tasks: PersonalTask[];
}
