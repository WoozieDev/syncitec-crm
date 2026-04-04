export interface ProjectClientOption {
    id: number;
    name: string;
    company: string | null;
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
