import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './views/Home';
import MyProducts from './views/MyProducts';
import PInfo from './views/ProductInformations';
import PCreate from './views/ProductCreation';
import ProducerRequest from './views/ProducerRequest';
import PayementSuccess from './views/PayementSuccess';
import Cart from './views/Cart';
import Profile from './views/Profile';

Vue.use(VueRouter);

const router = new VueRouter({
  mode: 'history',
  routes:[
    {path:'/', name:'home', component:Home},
    {path:'/product/my-products', name:'my-products', component:MyProducts},
    {path:'/product/products/:id', name:'product-info', component:PInfo},
    {path:'/product/new', name:'product-creation', component:PCreate},
    {path:'/compte', name:'profile', component:Profile},
    {path:'/producer/request', name:'producer-request', component:ProducerRequest},
    {path:'/cart', name:'cart', component:Cart},
    {path:'/payement-success', name:'PayementSuccess', component:PayementSuccess},
    {path:'/logout', name:'deconnexion'},
    // {path:'/connexion', name:'connect', component:Connect}
  ]
});

export default router;