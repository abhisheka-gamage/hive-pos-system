import { defineStore } from "pinia";
import Aura from "@primeuix/themes/aura";
import Material from "@primeuix/themes/material";

export type ThemePreset = "aura" | "material";

interface PrimeThemePreset {
  toggleDarkMode?: (dark: boolean) => void;
  apply?: () => void;
}

export const useThemeStore = defineStore("theme", {
  state: () => ({
    darkMode: localStorage.getItem("darkMode") === "true",
    preset: (localStorage.getItem("preset") as ThemePreset) || "aura",
  }),
  actions: {
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      localStorage.setItem("darkMode", String(this.darkMode));
      document.documentElement.classList.toggle("my-app-dark", this.darkMode);
      this.getPresetObject().toggleDarkMode?.(this.darkMode);
    },

    setPreset(preset: ThemePreset) {
      this.preset = preset;
      localStorage.setItem("preset", preset);

      this.getPresetObject().apply?.();
      this.getPresetObject().toggleDarkMode?.(this.darkMode);
    },

    getPresetObject(): PrimeThemePreset {
      switch (this.preset) {
        case "aura":
          return Aura as unknown as PrimeThemePreset;
        case "material":
          return Material as unknown as PrimeThemePreset;
        default:
          return Aura as unknown as PrimeThemePreset;
      }
    },
  },
});
