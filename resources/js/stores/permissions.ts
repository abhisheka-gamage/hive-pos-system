import { defineStore } from "pinia";
import { MenuItem } from "primevue/menuitem";

interface NavHeader {
    label: string
    icon: string
    items: {
        label: string
        items: {
            label: string
            command: () => void
        }[]
    }[]
}

export const PermissionStore = defineStore("permissions", {
  state: () => ({
    details: null as NavHeader[] | null,
    routePermissions: [] as string[],
  }),
  getters: {
    isLoaded: (state) => !!state.details,
    hasRoutePermission: (state) => (perm: string ) => state.routePermissions.includes(perm),
  },
  actions: {
    setPermission(permission: NavHeader[]) {
      this.details = permission;
    },
    setRoutePermissions(perms: string[]) {
      this.routePermissions = perms;
    },
    clearPermission() {
      this.details = null;
    }
  }
});
