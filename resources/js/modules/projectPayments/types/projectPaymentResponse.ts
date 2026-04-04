import type {
    ProjectPayment,
    ProjectPaymentProjectOption,
} from './projectPayment';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedProjectPayments {
    data: ProjectPayment[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface ProjectPaymentOverview {
    total_payments: number;
    total_paid: number;
    unique_projects: number;
    average_payment: number;
}

export interface ProjectPaymentIndexProps {
    payments: PaginatedProjectPayments;
    filters: {
        search: string;
        project_id: string;
        payment_method: string;
    };
    overview: ProjectPaymentOverview;
    projects: ProjectPaymentProjectOption[];
    payment_methods: string[];
}

export interface ProjectPaymentCreateProps {
    projects: ProjectPaymentProjectOption[];
    selected_project_id: number | null;
}

export interface ProjectPaymentEditProps extends ProjectPaymentCreateProps {
    payment: ProjectPayment;
}

export interface ProjectPaymentShowProps {
    payment: ProjectPayment;
}

