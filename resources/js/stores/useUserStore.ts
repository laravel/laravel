import { create } from "zustand";
import { persist } from "zustand/middleware";

export interface User {
  email: string;
  name: string;
  picture: string;
  role: "standard" | "admin";
}

export interface UserStoreState {
  user: User | null;
  token: string | null;
  setUser: (user: User | null) => void;
  setToken: (token: string | null) => void;
  clearUser: () => void;
}

export const useUserStore = create<UserStoreState>()(
  persist(
    (set) => ({
      user: null,
      token: null,
      setUser: (user: User | null) => {
        set(() => ({ user }));
      },
      setToken: (token: string | null) => {
        set(() => ({ token }));
      },
      clearUser: () => {
        set({ user: null, token: null });
      },
    }),
    {
      name: "feedbackUserStore",
    },
  ),
);
