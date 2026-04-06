export interface ProjectKanbanClient {
    id: number | null;
    name: string | null;
    company: string | null;
    email: string | null;
    phone: string | null;
    country: string | null;
    display_name: string;
}

export interface ProjectKanbanModule {
    id: number;
    name: string;
    order: number;
}

export interface ProjectKanbanOption {
    value: string;
    label: string;
}

export interface ProjectKanbanTaskModule {
    id: number;
    project_id: number;
    name: string;
    order: number;
}

export interface ProjectKanbanTask {
    id: number;
    project_id: number;
    module_id: number | null;
    title: string;
    description: string | null;
    status: string;
    status_label: string;
    is_completed: boolean;
    priority: string | null;
    order: number;
    created_at?: string | null;
    updated_at?: string | null;
    module: ProjectKanbanTaskModule | null;
}

export interface ProjectKanbanColumn {
    id: number | null;
    key: string;
    name: string;
    label: string;
    order: number | null;
    count: number;
    is_unassigned: boolean;
    can_delete: boolean;
    tasks: ProjectKanbanTask[];
}

export interface ProjectKanbanViewOption {
    key: string;
    label: string;
}

export interface ProjectKanbanProject {
    id: number;
    name: string;
    description: string | null;
    status: string;
    status_label: string;
    status_tone: string;
    start_date: string | null;
    due_date: string | null;
    client: ProjectKanbanClient;
    modules: ProjectKanbanModule[];
    overview: {
        tasks_total: number;
        modules_total: number;
        completed_tasks: number;
        active_tasks: number;
        unassigned_tasks: number;
    };
    tasks: ProjectKanbanTask[];
}

export interface ProjectKanbanTaskFormData {
    project_id: string;
    module_id: string;
    title: string;
    description: string;
    status: string;
    priority: string;
    order: string;
}

export interface ProjectKanbanColumnFormData {
    name: string;
    order: string;
}

export interface ProjectKanbanProps {
    project: ProjectKanbanProject;
    view: string;
    views: ProjectKanbanViewOption[];
    board: ProjectKanbanColumn[];
    status_options: ProjectKanbanOption[];
    priority_options: ProjectKanbanOption[];
}
