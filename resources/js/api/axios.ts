import axios from "axios";

import { env } from "@/env";
import { errorResponse, privateRequest } from "./interceptors";

const baseConfig = {
  baseURL: env.VITE_API_URL,
  headers: {
    "Content-Type": "application/json",
  },
};

export const publicAPI = axios.create(baseConfig);
export const privateAPI = axios.create(baseConfig);

privateAPI.interceptors.request.use(privateRequest);
privateAPI.interceptors.response.use((response) => response, errorResponse);
