import { useLocation, useNavigate } from "react-router-dom";

import type { MODAL_ROUTES } from "./routes";

type ModalRoutes = (typeof MODAL_ROUTES)[keyof typeof MODAL_ROUTES];
type ValidModalUrl<T extends string> = T extends `${infer _}/${infer _}`
  ? never
  : ModalRoutes | `${ModalRoutes}/${T}` | `${ModalRoutes}/${T}/${T}`;

export const useNavigateModal = () => {
  const location = useLocation();
  const navigate = useNavigate();

  const { previousLocation } = (location.state ?? {}) as {
    previousLocation?: Location;
  };

  // we make normal routing work as well as param routing, but make multiple params invalid
  return <T extends string>(
    url: ValidModalUrl<T>,
    state?: Record<string, unknown>,
  ) => {
    navigate(url, {
      state: { ...state, previousLocation: previousLocation ?? location },
    });
  };
};
