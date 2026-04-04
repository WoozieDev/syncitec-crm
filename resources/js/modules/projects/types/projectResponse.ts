import type {
    ProjectDetail,
    Project,
    ProjectClientOption,
    ProjectStatusOption,
} from './project';

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedProjects {
    data: Project[];
    links: PaginationLink[];
    total?: number;
    from?: number | null;
    to?: number | null;
    current_page?: number;
    last_page?: number;
    per_page?: number;
}

export interface ProjectOverview {
    total_value: number;
    in_review: number;
    due_today: number;
}

export interface ProjectViewFilter {
    key: string;
    label: string;
    count: number;
}

export interface ProjectIndexProps {
    projects: PaginatedProjects;
    filters: {
        search: string;
        view: string;
    };
    overview: ProjectOverview;
    views: ProjectViewFilter[];
}

export interface ProjectCreateProps {
    clients: ProjectClientOption[];
    status_options: ProjectStatusOption[];
}

export interface ProjectEditProps extends ProjectCreateProps {
    project: Project;
}

export interface ProjectShowProps {
    project: ProjectDetail;
}
