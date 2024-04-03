import type { QueryClient } from "@tanstack/react-query";

import type { ServiceResponse } from "./api.types";
import { privateAPI } from "./axios";

const DOMAIN = "user";
const ALL = "all";

export interface User {
  id: number;
  name: string;
  email: string;
}

export const getUsersQuery = () => ({
  queryKey: [DOMAIN, ALL, "getUsersQuery"],
  queryFn: async () => {
    const response = await privateAPI.get<ServiceResponse<User[]>>("/users");

    return response.data.data;
  },
});

export const getUserQuery = (userId: User["id"]) => ({
  queryKey: [DOMAIN, userId, "getUserQuery"],
  queryFn: async () => {
    const response = await privateAPI.get<ServiceResponse<User>>(
      `/users/${userId}`,
    );

    return response.data.data;
  },
});

interface CreateUserParams {
  name: string;
  email: string;
  password: string;
  passwordConfirmation: string;
}

export const createUser = {
  mutation: async (params: CreateUserParams) => {
    const { passwordConfirmation, ...rest } = params;
    const response = await privateAPI.post<ServiceResponse<User>>("/users", {
      ...rest,
      password_confirmation: passwordConfirmation,
    });

    return response.data.data;
  },
  invalidates: (queryClient: QueryClient) => {
    void queryClient.invalidateQueries({ queryKey: [DOMAIN, ALL] });
  },
};

export const deleteUser = {
  mutation: async (userId: User["id"]) => {
    await privateAPI.delete(`/users/${userId}`);
  },
  invalidates: (
    queryClient: QueryClient,
    { userId }: { userId: User["id"] },
  ) => {
    void queryClient.invalidateQueries({ queryKey: [DOMAIN, ALL] });
    void queryClient.invalidateQueries({ queryKey: [DOMAIN, userId] });
  },
};
