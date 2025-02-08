/// <reference types="vitest/config" />
import { defineConfig } from "vite";
import { configDefaults } from "vitest/config";

export default defineConfig({
  test: {
    dir: "./src/__tests__",
    globals: true,
    environment: "jsdom",
    setupFiles: ["./src/vitest-setup.ts"],
    exclude: [
      ...configDefaults.exclude,
      "**/playwright/**",
      "**/playwright-report/**",
      "**/test-results/**",
    ],
    coverage: {
      reporter: ["text"],
      exclude: [
        ...configDefaults.exclude,
        "**/playwright/**",
        "**/playwright-report/**",
        "**/test-results/**",
        "**/e2e/**",
        "**playwright.config.ts",
        "**/exceptions/**",
      ],
      thresholds: {
        branches: 80,
        functions: 80,
        lines: 80,
        statements: 80,
      },
    },
  },
});
