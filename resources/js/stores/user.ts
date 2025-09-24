import { defineStore } from 'pinia';

interface User {
  id: number;
  name: string;
  email: string;
}

export const useUserStore = defineStore('user', {
  state: () => ({
    details: null as User | null,
    fetched: false,
  }),
  getters: {
    isLoaded: (state) => !!state.details,
  },
  actions: {
    setUser(user: User) {
      this.details = user;
      this.fetched = true;
    },
    clearUser() {
      this.details = null;
      this.fetched = false;
    }
  }
});
