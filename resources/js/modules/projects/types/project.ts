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

export interface ProjectClientDetail extends ProjectClient {
    email: string | null;
    phone: string | null;
    country: string | null;
}

export interface ProjectFinancialSummary {
    total_price: number;
    total_paid: number;
    pending_balance: number;
}

export interface ProjectPayment {
    id: number;
    amount: number;
    payment_date: string | null;
    payment_method: string;
    notes: string | null;
    created_at?: string | null;
    updated_at?: string | null;
}

export interface ProjectModule {
    id: number;
    name: string;
    order: number;
}

export interface ProjectTaskModule {
    id: number;
    name: string;
    order: number;
}

export interface ProjectTask {
    id: number;
    module_id: number | null;
    title: string;
    description: string | null;
    status: string;
    priority: string | null;
    order: number;
    module: ProjectTaskModule | null;
    created_at?: string | null;
    updated_at?: string | null;
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

export interface ProjectDetail extends Project {
    client: ProjectClientDetail;
    financial_summary: ProjectFinancialSummary;
    payments: ProjectPayment[];
    modules: ProjectModule[];
    tasks: ProjectTask[];
}
