import type { LucideIcon } from 'lucide-vue-next';

export interface StatCardItem {
    title: string;
    value: string | number;
    description: string;
    icon: LucideIcon;
    badge?: string;
    badgeVariant?: 'default' | 'success' | 'warning' | 'danger';
    tone?: 'primary' | 'sky' | 'danger' | 'slate' | 'indigo';
    hasAlertDot?: boolean;
}

export interface OverduePaymentItem {
    id: number;
    customer: string;
    companyId: string;
    service: string;
    amount: string;
    dueDate: string;
    status: string;
    initials: string;
    avatarTone?: 'primary' | 'sky' | 'slate';
}

export interface UrgentTaskItem {
    id: number;
    title: string;
    description: string;
    priority: string;
    dueLabel?: string;
    tone?: 'danger' | 'primary' | 'slate';
}

export interface ActivityItem {
    id: number;
    dateLabel: string;
    title: string;
    description: string;
    dotVariant?: 'primary' | 'success' | 'muted';
}

export interface RenewalItem {
    id: number;
    name: string;
    date: string;
}
