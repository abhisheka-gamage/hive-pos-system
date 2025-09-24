import { defineStore } from "pinia";

export const useThrobber = defineStore("loading", {
  state: () => ({
    details: false as boolean,
  }),
  getters: {
    isLoaded: (state) => !!state.details,
  },
  actions: {
    setStatus(status: boolean) {
      this.details = status;
    }
  }
});
