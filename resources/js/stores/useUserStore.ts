import { create } from "zustand";
import { persist } from "zustand/middleware";

interface UserStore {
  token: string | null;

  setToken: (token: string | null) => void;
}

export const useUserStore = create<UserStore>()(
  persist(
    (set) => ({
      token: null,
      setToken: (token: string | null) => {
        set(() => ({ token }));
      },
    }),
    {
      name: "useUserStore",
    },
  ),
);
