import type {
    ProviderOption,
    Service,
    ServiceClientOption,
    ServiceDetail,
    ServiceOverview,
    ServiceSelectOption,
    ServiceTypeOption,
} from './service';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedServices {
    data: Service[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface ServiceIndexProps {
    services: PaginatedServices;
    filters: {
        search: string;
        client_id: string;
        status: string;
        service_type_id: string;
    };
    overview: ServiceOverview;
    clients: ServiceClientOption[];
    service_types: ServiceTypeOption[];
    status_options: ServiceSelectOption[];
}

export interface ServiceCreateProps {
    clients: ServiceClientOption[];
    service_types: ServiceTypeOption[];
    providers: ProviderOption[];
    billing_type_options: ServiceSelectOption[];
    billing_cycle_options: ServiceSelectOption[];
    status_options: ServiceSelectOption[];
}

export interface ServiceEditProps extends ServiceCreateProps {
    service: Service;
}

export interface ServiceShowProps {
    service: ServiceDetail;
}
