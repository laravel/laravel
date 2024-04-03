import { Outlet } from "react-router-dom";

import { Navbar } from "./Navbar";

export const Layout = () => {
  return (
    <div className="h-screen flex-col overflow-hidden bg-gray-900 md:flex md:flex-row">
      <Navbar />
      <main className="h-full grow overflow-y-auto">
        <Outlet />
      </main>
    </div>
  );
};
