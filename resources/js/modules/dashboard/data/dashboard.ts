import {
    CircleAlert,
    ClipboardList,
    RefreshCcw,
    TriangleAlert,
    Users,
    WalletCards,
} from 'lucide-vue-next';
import type {
    ActivityItem,
    OverduePaymentItem,
    RenewalItem,
    StatCardItem,
    UrgentTaskItem,
} from '@/modules/dashboard/types/dashboard';

export const stats: StatCardItem[] = [
    {
        title: 'Total Customers',
        value: '1,284',
        description: 'Total customers',
        icon: Users,
        badge: '+12%',
        badgeVariant: 'success',
        tone: 'primary',
    },
    {
        title: 'Active Projects',
        value: '42',
        description: 'Active projects',
        icon: ClipboardList,
        badge: '4 New',
        tone: 'slate',
    },
    {
        title: 'Pending Installments',
        value: '18',
        description: 'Pending installments',
        icon: WalletCards,
        tone: 'sky',
    },
    {
        title: 'Overdue Payments',
        value: '07',
        description: 'Overdue payments',
        icon: TriangleAlert,
        badge: 'High',
        badgeVariant: 'danger',
        tone: 'danger',
    },
    {
        title: 'Renewing Soon',
        value: '15',
        description: 'Renewing soon',
        icon: RefreshCcw,
        tone: 'slate',
    },
    {
        title: 'Urgent Tasks',
        value: '03',
        description: 'Urgent tasks',
        icon: CircleAlert,
        tone: 'indigo',
        hasAlertDot: true,
    },
];

export const overduePayments: OverduePaymentItem[] = [
    {
        id: 1,
        customer: 'Aether Labs',
        companyId: 'Corp ID: #8821',
        service: 'Cloud Infrastructure',
        amount: '$2,450.00',
        dueDate: 'Oct 12, 2023',
        status: 'Overdue',
        initials: 'AL',
        avatarTone: 'sky',
    },
    {
        id: 2,
        customer: 'Meta Sphere',
        companyId: 'Corp ID: #1024',
        service: 'Monthly Retainer',
        amount: '$5,000.00',
        dueDate: 'Oct 15, 2023',
        status: 'Overdue',
        initials: 'MS',
        avatarTone: 'primary',
    },
    {
        id: 3,
        customer: 'Jupiter Designs',
        companyId: 'Corp ID: #5512',
        service: 'Logo Refresh',
        amount: '$850.00',
        dueDate: 'Oct 16, 2023',
        status: 'Overdue',
        initials: 'JD',
        avatarTone: 'slate',
    },
];

export const urgentTasks: UrgentTaskItem[] = [
    {
        id: 1,
        title: 'Review Meta Sphere Contract',
        description: 'Pending approval for next phase',
        priority: 'Critical',
        dueLabel: 'Due in 2h',
        tone: 'danger',
    },
    {
        id: 2,
        title: 'Customer Onboarding: AL',
        description: 'Upload preliminary documentation',
        priority: 'Operational',
        dueLabel: 'Due today',
        tone: 'primary',
    },
    {
        id: 3,
        title: 'Update Renewal Dates',
        description: 'Sync Q4 service schedules',
        priority: 'Admin',
        tone: 'slate',
    },
];

export const recentActivities: ActivityItem[] = [
    {
        id: 1,
        dateLabel: 'Today, 10:45 AM',
        title: 'Payment Received from Nova Soft',
        description: 'Installment #3 of Web Development',
        dotVariant: 'primary',
    },
    {
        id: 2,
        dateLabel: 'Yesterday, 4:20 PM',
        title: 'New Lead: Spark Systems',
        description: 'Assigned to: Marcus Aurelius',
        dotVariant: 'success',
    },
    {
        id: 3,
        dateLabel: 'Yesterday, 9:15 AM',
        title: 'Project "Aurora" Milestone Reached',
        description: 'Design phase completed by Sarah Jones',
        dotVariant: 'muted',
    },
];

export const renewals: RenewalItem[] = [
    { id: 1, name: 'Enterprise Hosting - GigaTech', date: 'Oct 28' },
    { id: 2, name: 'SaaS Pro License - Void Corp', date: 'Oct 30' },
];
