import type {
    ServicePayment,
    ServicePaymentProviderOption,
    ServicePaymentServiceOption,
} from './servicePayment';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedServicePayments {
    data: ServicePayment[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface ServicePaymentOverview {
    total_payments: number;
    total_paid: number;
    unique_services: number;
    average_payment: number;
}

export interface ServicePaymentIndexProps {
    payments: PaginatedServicePayments;
    filters: {
        search: string;
        service_id: string;
        payment_method: string;
        provider_id: string;
    };
    overview: ServicePaymentOverview;
    services: ServicePaymentServiceOption[];
    providers: ServicePaymentProviderOption[];
    payment_methods: string[];
}

export interface ServicePaymentCreateProps {
    services: ServicePaymentServiceOption[];
    selected_service_id: number | null;
}

export interface ServicePaymentEditProps extends ServicePaymentCreateProps {
    payment: ServicePayment;
}

export interface ServicePaymentShowProps {
    payment: ServicePayment;
}
