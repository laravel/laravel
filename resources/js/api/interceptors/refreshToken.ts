import mem from "mem";

import { useUserStore } from "@/stores";
import type { ServiceResponse } from "../api.types";
import { privateAPI } from "../axios";

export interface UserToken {
  refreshToken: string;
  tokenType: string;
  expiresIn: number;
}

const refreshToken = async () => {
  const { setToken, clearUser } = useUserStore.getState();
  let refreshWasSuccessful = false;

  try {
    const response =
      await privateAPI.post<ServiceResponse<UserToken>>("/auth/refresh");

    const { data: userToken } = response.data;
    if (!userToken.refreshToken) {
      clearUser();
    } else {
      refreshWasSuccessful = true;
      setToken(userToken.refreshToken);
    }
  } catch (error) {
    clearUser();
  }

  return refreshWasSuccessful;
};

const MAX_AGE = 10000 as const;

export const memoizedRefreshToken = mem(refreshToken, { maxAge: MAX_AGE });
