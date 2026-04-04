export interface Client {
    id: number;
    name: string;
    company: string | null;
    email: string;
    phone: string | null;
    country: string | null;
    notes: string | null;
    created_at?: string | null;
    updated_at?: string | null;
}

export interface ClientProject {
    id: number;
    project_code: string;
    name: string;
    status: string;
    status_label: string;
    status_tone: string;
    total_amount: number;
    paid_amount: number;
    balance_due: number;
    start_date: string | null;
    due_date: string | null;
    created_at?: string | null;
}

export interface ClientService {
    id: number;
    name: string;
    service_type: string | null;
    provider: string | null;
    billing_type: string;
    billing_cycle: string | null;
    price: number;
    paid_amount: number;
    status: string;
    start_date: string | null;
    next_renewal_date: string | null;
    notes: string | null;
}

export interface ClientPayment {
    id: string;
    source_type: string;
    source_label: string;
    source_name: string;
    amount: number;
    payment_date: string | null;
    payment_method: string;
    notes: string | null;
}

export interface ClientFinancialSummary {
    total_received: number;
    project_value: number;
    project_paid: number;
    project_balance: number;
    service_value: number;
    service_paid: number;
    payments_count: number;
    recurring_services_count: number;
    recurring_services_value: number;
}

export interface ClientDetail extends Client {
    financial_summary: ClientFinancialSummary;
    projects: ClientProject[];
    services: ClientService[];
    recent_payments: ClientPayment[];
}
