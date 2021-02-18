import PersonalData from "../components/wizard/steps/PersonalData.vue";
import ResumePayment from "../components/wizard/steps/ResumePayment";
import OrderPlaced from "../components/wizard/steps/OrderPlaced";
import WizardBase from "../components/wizard/WizardBase";

const routes = [
    {
        path: "/wizard",
        component: WizardBase,
            children:[
                {
                    path: "/personal-data",
                    name: "personal-data",
                    component: PersonalData
                },
                {
                    path: "/resume-and-payment",
                    name: "resume-payment",
                    component: ResumePayment
                },
                {
                    path: "/order-placed",
                    name: "order-placed",
                    component: OrderPlaced
                }
            ]

    },
]
export default routes;
