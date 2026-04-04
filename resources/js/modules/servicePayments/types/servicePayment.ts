export interface ServicePaymentClient {
    id: number | null;
    name: string | null;
    company: string | null;
    email?: string | null;
    phone?: string | null;
    country?: string | null;
    display_name: string;
}

export interface ServicePaymentServiceType {
    id: number | null;
    name: string | null;
}

export interface ServicePaymentProvider {
    id: number | null;
    name: string | null;
}

export interface ServicePaymentServiceOption {
    id: number;
    name: string;
    client: ServicePaymentClient;
    service_type: ServicePaymentServiceType;
    provider: ServicePaymentProvider | null;
    billing_type: string;
    billing_type_label: string;
    billing_cycle: string | null;
    billing_cycle_label: string | null;
    status: string;
    status_label: string;
    status_tone: string;
    price: number;
    paid_amount: number;
    balance_due: number;
    next_renewal_date: string | null;
    label: string;
}

export interface ServicePaymentService {
    id: number;
    name: string;
    status?: string;
    status_label?: string;
    status_tone?: string;
    price?: number;
    paid_amount?: number;
    balance_due?: number;
    next_renewal_date?: string | null;
    billing_type?: string;
    billing_type_label?: string;
    billing_cycle?: string | null;
    billing_cycle_label?: string | null;
    client: ServicePaymentClient;
    service_type: ServicePaymentServiceType;
    provider: ServicePaymentProvider | null;
}

export interface ServicePaymentProviderOption {
    id: number;
    name: string;
    label: string;
}

export interface ServicePayment {
    id: number;
    amount: number;
    payment_date: string | null;
    payment_method: string;
    notes: string | null;
    created_at?: string | null;
    updated_at?: string | null;
    service: ServicePaymentService | null;
}
