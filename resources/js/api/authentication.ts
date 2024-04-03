import { useUserStore } from "@/stores";
import type { ServiceResponse } from "./api.types";
import { publicAPI } from "./axios";

interface UserToken {
  accessToken: string;
  tokenType: string;
  expiresIn: number;
}

interface GoogleLoginRequest {
  email: string;
  name: string;
  googleToken: string;
}

export const googleLogin = {
  mutation: async (params: GoogleLoginRequest) => {
    const response = await publicAPI.post<ServiceResponse<UserToken>>(
      "/auth/google",
      {
        ...params,
      },
    );
    return response.data;
  },
};

export const logout = () => {
  const { clearUser } = useUserStore.getState();

  clearUser();
};
