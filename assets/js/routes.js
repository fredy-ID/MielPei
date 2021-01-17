import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './components/Home';

Vue.use(VueRouter);

const router = new VueRouter({
  mode: 'history',
  routes:[
    {path:'/', name:'home', component:Home},
    // {path:'/connexion', name:'connect', component:Connect}
  ]
});

export default router;