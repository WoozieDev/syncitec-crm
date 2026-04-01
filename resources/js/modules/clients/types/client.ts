export interface Client {
    id: number;
    name: string;
    company: string;
    email: string;
    phone: string;
    country: string;
    notes: string | null;
    created_at?: string | null;
    updated_at?: string | null;
}
