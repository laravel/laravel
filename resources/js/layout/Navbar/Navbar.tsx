import { ResponsiveSidebar } from "./ResponsiveSidebar";
import { Sidebar } from "./Sidebar";

export const Navbar = () => {
  return (
    <>
      <ResponsiveSidebar />

      <div className="hidden h-screen md:block">
        <Sidebar />
      </div>
    </>
  );
};
