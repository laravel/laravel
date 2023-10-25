import axios from "axios";

import { useUserStore } from "@/stores";

export const api = axios.create({
  // baseURL: env.VITE_API_URL,
  headers: {
    "Content-Type": "application/json",
  },
});

export const getAuthHeaders = () => {
  const userToken = useUserStore.getState().token;

  return {
    Authorization: `Bearer ${userToken}`,
  };
};

export interface ServiceResponse<T> {
  status: number;
  success: boolean;
  data: T;
  pagination?: {
    count: number;
    total: number;
    perPage: number;
    currentPage: number;
    totalPages: number;
    links: {
      previous: string;
      next: string;
    };
  };
}
