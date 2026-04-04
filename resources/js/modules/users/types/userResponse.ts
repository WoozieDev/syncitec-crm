import type { UserDetail, UserRecord } from './user';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedUsers {
    data: UserRecord[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface UserIndexProps {
    users: PaginatedUsers;
    filters: {
        search: string;
    };
}

export interface UserShowProps {
    user: UserDetail;
}
