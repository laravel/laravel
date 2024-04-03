import { Navigate, Route, Routes, useLocation } from "react-router-dom";
import type { Location } from "react-router-dom";

import { Layout } from "@/layout";
import { Home, NotFound, Users } from "@/screens";
import { Login } from "@/screens/Login";
import { ModalRouter } from "./ModalRouter";
import { ProtectedRoute } from "./ProtectedRoute";
import { ROUTES } from "./routes";

export const Router = () => {
  const location = useLocation();
  const { previousLocation } = (location.state ?? {}) as {
    previousLocation?: Location;
  };

  return (
    <>
      {/* PUBLIC ONLY ROUTES */}
      <Routes location={previousLocation ?? location}>
        <Route element={<ProtectedRoute expected="loggedOut" />}>
          <Route element={<Login />} path={ROUTES.login} />
        </Route>

        {/* PRIVATE ONLY ROUTES */}
        <Route element={<ProtectedRoute expected={["admin", "standard"]} />}>
          <Route element={<Layout />}>
            <Route element={<Navigate to={ROUTES.home} />} path={ROUTES.base} />

            <Route element={<Home />} path={ROUTES.home} />

            <Route path={ROUTES.notFound} element={<NotFound />} />
          </Route>
        </Route>

        <Route element={<ProtectedRoute expected="admin" />}>
          <Route element={<Layout />}>
            <Route element={<Users />} path={ROUTES.users} />
          </Route>
        </Route>
      </Routes>

      {/* MODALS ROUTES */}
      <Routes>
        <Route
          path="*"
          element={<ModalRouter showModal={!!previousLocation} />}
        />
      </Routes>
    </>
  );
};
