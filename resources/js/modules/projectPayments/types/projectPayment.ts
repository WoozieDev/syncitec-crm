export interface ProjectPaymentClient {
    id: number | null;
    name: string | null;
    company: string | null;
    email?: string | null;
    phone?: string | null;
    country?: string | null;
    display_name: string;
}

export interface ProjectPaymentProjectOption {
    id: number;
    name: string;
    client_name: string;
    client_display_name: string;
    status: string;
    total_amount: number;
    paid_amount: number;
    balance_due: number;
    label: string;
}

export interface ProjectPaymentProject {
    id: number;
    name: string;
    status?: string;
    total_amount?: number;
    paid_amount?: number;
    balance_due?: number;
    client: ProjectPaymentClient;
}

export interface ProjectPayment {
    id: number;
    amount: number;
    payment_date: string | null;
    payment_method: string;
    notes: string | null;
    created_at?: string | null;
    updated_at?: string | null;
    project: ProjectPaymentProject | null;
}

