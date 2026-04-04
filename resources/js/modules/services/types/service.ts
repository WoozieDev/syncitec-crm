export interface ServiceClientOption {
    id: number;
    name: string;
    company: string | null;
    label: string;
}

export interface ServiceTypeOption {
    id: number;
    name: string;
    label: string;
}

export interface ProviderOption {
    id: number;
    name: string;
    label: string;
}

export interface ServiceSelectOption {
    value: string;
    label: string;
}

export interface ServiceClient {
    id: number | null;
    name: string | null;
    company: string | null;
    display_name: string;
}

export interface ServiceClientDetail extends ServiceClient {
    email: string | null;
    phone: string | null;
    country: string | null;
}

export interface ServiceTypeSummary {
    id: number | null;
    name: string | null;
}

export interface ProviderSummary {
    id: number | null;
    name: string | null;
}

export interface Service {
    id: number;
    name: string;
    description: string | null;
    billing_type: string;
    billing_type_label: string;
    billing_cycle: string | null;
    billing_cycle_label: string | null;
    price: number;
    cost: number | null;
    start_date: string | null;
    next_renewal_date: string | null;
    renewal_state: string;
    renewal_label: string;
    status: string;
    status_label: string;
    status_tone: string;
    notes: string | null;
    client: ServiceClient;
    service_type: ServiceTypeSummary;
    provider: ProviderSummary | null;
    created_at?: string | null;
    updated_at?: string | null;
}

export interface ServiceDetail extends Service {
    client: ServiceClientDetail;
    margin_amount: number;
    margin_percentage: number | null;
}

export interface ServiceOverview {
    total_services: number;
    active_services: number;
    expiring_services: number;
    expired_services: number;
}
