export interface ProjectClientOption {
    id: number;
    name: string;
    company: string;
    label: string;
}

export interface ProjectStatusOption {
    value: string;
    label: string;
}

export interface ProjectClient {
    id: number | null;
    name: string | null;
    company: string | null;
    display_name: string;
}

export interface Project {
    id: number;
    project_code: string;
    name: string;
    description: string;
    status: string;
    status_label: string;
    status_tone: string;
    progress_percent: number;
    progress_label: string;
    total_amount: number;
    paid_amount: number;
    balance_due: number;
    start_date: string | null;
    due_date: string | null;
    is_due_today: boolean;
    notes: string | null;
    client: ProjectClient;
    created_at?: string | null;
    updated_at?: string | null;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedProjects {
    data: Project[];
    links: PaginationLink[];
}

export interface ProjectOverview {
    total_value: number;
    in_review: number;
    due_today: number;
}

export interface ProjectViewFilter {
    key: string;
    label: string;
    count: number;
}

export interface ProjectIndexProps {
    projects: PaginatedProjects;
    filters: {
        search: string;
        view: string;
    };
    overview: ProjectOverview;
    views: ProjectViewFilter[];
}

export interface ProjectFormData {
    client_id: number | '';
    name: string;
    description: string;
    status: string;
    price: number | '';
    start_date: string;
    due_date: string;
    notes: string;
}

export interface ProjectCreateProps {
    clients: ProjectClientOption[];
    status_options: ProjectStatusOption[];
}

export interface ProjectEditProps extends ProjectCreateProps {
    project: Project;
}

export interface ProjectShowProps {
    project: Project;
}
