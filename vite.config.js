/* eslint-disable import/no-extraneous-dependencies */
import react from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import checker from "vite-plugin-checker";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/App.tsx"],
      refresh: true,
    }),
    react(),
    checker({ typescript: true }),
    {
      handleHotUpdate({ file, server }) {
        if (file.endsWith(".blade.php")) {
          server.ws.send({ path: "*", type: "full-reload" });
        }
      },
      name: "blade",
    },
  ],
});
