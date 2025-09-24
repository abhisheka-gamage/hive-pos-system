import { defineStore } from "pinia";
import { MenuItem } from "primevue/menuitem";


export const PermissionStore = defineStore("permissions", {
  state: () => ({
    details: null as MenuItem[] | null,
    routePermissions: [] as string[],
  }),
  getters: {
    isLoaded: (state) => !!state.details,
    hasRoutePermission: (state) => (perm: string ) => state.routePermissions.includes(perm),
  },
  actions: {
    setPermission(permission: MenuItem[]) {
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
