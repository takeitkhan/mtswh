import userIndex from "../components/user/index.vue";
import userForm from "../components/user/form.vue";

export const routes = [
    //User
    {
        path: '/users',
        component: userIndex,
        name: 'user_index',
    },
    {
        path: '/user-add',
        component: userForm,
        name: 'user_add',
    },

]