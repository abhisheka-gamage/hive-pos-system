export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

interface NavSubItem {
  id: number;
  display_name: string;
  code: string;
  url: string;
  permission: string[];
}

interface NavItem {
  map(arg0: (sub_menus: any) => { id: any; label: string; command: () => any; }): any;
  id: number;
  display_name: string;
  children: NavSubItem[];
}

interface NavHeader {
  id: number;
  display_name: string;
  children: NavItem[];
  icon: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
        permissions: string[];
        nav_data: NavHeader[]
    };
};
