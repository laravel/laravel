import ClientBase from "../components/client/ClientBase";
import UserData from "../components/client/pages/UserData";

const routes = [
    {
        path: "/client",
        component: ClientBase,
            children:[
                {
                    path: "/user-data",
                    name: "user-data",
                    component: UserData
                },
                {
                    path: "/user-orders",
                    name: "user-orders",
                    props: true,
                    component: UserOrders
                },

            ]

    },
]
export default routes;
