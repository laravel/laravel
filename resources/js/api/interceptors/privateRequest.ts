import type { InternalAxiosRequestConfig } from "axios";

import { useUserStore } from "@/stores";

export const privateRequest = (config: InternalAxiosRequestConfig) => {
  const { token } = useUserStore.getState();

  if (token) {
    config.headers.set("Authorization", `Bearer ${token}`);
  }
  return config;
};
