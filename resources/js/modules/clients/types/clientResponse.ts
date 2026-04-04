import type { Client, ClientDetail } from './client';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedClients {
    data: Client[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface ClientIndexProps {
    clients: PaginatedClients;
    filters: {
        search: string;
    };
}

export interface ClientShowProps {
    client: ClientDetail;
}
