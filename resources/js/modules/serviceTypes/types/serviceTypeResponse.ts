import type { ServiceType } from './serviceType';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedServiceTypes {
    data: ServiceType[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface ServiceTypeIndexProps {
    service_types: PaginatedServiceTypes;
    filters: {
        search: string;
    };
}

export interface ServiceTypeEditProps {
    service_type: ServiceType;
}
