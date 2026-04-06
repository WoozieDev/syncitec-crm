export interface ProfileUser {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
}

export interface ProfilePageProps {
    user: ProfileUser;
    mustVerifyEmail: boolean;
    status?: string;
}
