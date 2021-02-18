import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './views/Home';
import MyProducts from './views/MyProducts';

Vue.use(VueRouter);

const router = new VueRouter({
  mode: 'history',
  routes:[
    {path:'/', name:'home', component:Home},
    {path:'/product/my-products', name:'my-products', component:MyProducts},
    // {path:'/connexion', name:'connect', component:Connect}
  ]
});

export default router;