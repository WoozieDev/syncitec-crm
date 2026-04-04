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
    order: number;
    created_at?: string | null;
    updated_at?: string | null;
}
