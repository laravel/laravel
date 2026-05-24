import { Link, useRouterState } from "@tanstack/react-router";
import { LayoutDashboard, Users, PawPrint, Scissors, CalendarDays } from "lucide-react";

import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar";

const items = [
  { title: "Dashboard", url: "/", icon: LayoutDashboard },
  { title: "Clientes", url: "/clientes", icon: Users },
  { title: "Pets", url: "/pets", icon: PawPrint },
  { title: "Serviços", url: "/servicos", icon: Scissors },
  { title: "Agendamentos", url: "/agendamentos", icon: CalendarDays },
];

export function AppSidebar() {
  const currentPath = useRouterState({ select: (r) => r.location.pathname });

  return (
    <Sidebar collapsible="icon">
      {/* Dynamic Mesh Background */}
      <div className="pointer-events-none absolute inset-0 overflow-hidden opacity-40 group-data-[collapsible=icon]:opacity-20">
        <div className="absolute -top-20 -left-20 h-64 w-64 rounded-full bg-primary opacity-10 blur-[80px]" />
        <div className="absolute top-1/2 -right-20 h-48 w-48 rounded-full bg-accent opacity-10 blur-[80px]" />
        <div className="absolute -bottom-20 left-1/2 h-56 w-56 rounded-full bg-primary opacity-5 blur-[80px]" />
      </div>

      <SidebarHeader className="relative z-10">
        <div className="flex items-center gap-2 px-2 py-3">
          <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-lg shadow-primary/20">
            <PawPrint className="h-5 w-5" />
          </div>
          <div className="flex flex-col leading-tight group-data-[collapsible=icon]:hidden">
            <span className="font-semibold">PetCare</span>
            <span className="text-xs text-muted-foreground">Pet Shop</span>
          </div>
        </div>
      </SidebarHeader>
      <SidebarContent className="relative z-10">
        <SidebarGroup>
          <SidebarGroupLabel>Navegação</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              {items.map((item) => {
                const active = item.url === "/" ? currentPath === "/" : currentPath.startsWith(item.url);
                return (
                  <SidebarMenuItem key={item.title}>
                    <SidebarMenuButton asChild isActive={active} tooltip={item.title}>
                      <Link to={item.url}>
                        <item.icon />
                        <span>{item.title}</span>
                      </Link>
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                );
              })}
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>
    </Sidebar>
  );
}
