export interface ProjectTaskClient {
    id: number | null;
    name: string | null;
    company: string | null;
    email?: string | null;
    phone?: string | null;
    country?: string | null;
    display_name: string;
}

export interface ProjectTaskStatusOption {
    value: string;
    label: string;
}

export interface ProjectTaskPriorityOption {
    value: string;
    label: string;
}

export interface ProjectTaskModule {
    id: number;
    project_id: number;
    name: string;
    order: number;
}

export interface ProjectTaskProjectOption {
    id: number;
    name: string;
    status: string;
    start_date: string | null;
    due_date: string | null;
    client: ProjectTaskClient;
    modules: ProjectTaskModule[];
    label: string;
}

export interface ProjectTaskProject {
    id: number;
    name: string;
    status: string;
    start_date: string | null;
    due_date: string | null;
    client: ProjectTaskClient;
}

export interface ProjectTask {
    id: number;
    project_id: number;
    module_id: number | null;
    title: string;
    description: string | null;
    status: string;
    priority: string | null;
    order: number;
    created_at?: string | null;
    updated_at?: string | null;
    project: ProjectTaskProject | null;
    module: ProjectTaskModule | null;
}
