import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './views/Home';
import Admin from './views/Admin';
import MyProducts from './views/MyProducts';
import Producer from './views/Producer';
import PInfo from './views/ProductInformations';
import PCreate from './views/ProductCreation';
import ProducerRequest from './views/ProducerRequest';
import PayementSuccess from './views/PayementSuccess';
import Cart from './views/Cart';
import Commands from './views/Commands';
import Profile from './views/Profile';

Vue.use(VueRouter);



const router = new VueRouter({
  mode: 'history',
  routes:[
    {path:'/', name:'home', component:Home},
    {path:'/admin', name:'admin', component:Admin},
    {path:'/producer/products', name:'my-products', component:MyProducts},
    {path:'/producer', name:'producer', component:Producer},
    {path:'/product/products/:id', name:'product-info', component:PInfo},
    {path:'/product/new', name:'product-creation', component:PCreate},
    {path:'/compte', name:'profile', component:Profile},
    {path:'/producer/request', name:'producer-request', component:ProducerRequest},
    {path:'/cart', name:'cart', component:Cart},
    {path:'/commands', name:'commands', component:Commands},
    {path:'/payement-success', name:'PayementSuccess', component:PayementSuccess},
    {path:'/logout', name:'deconnexion'},
    // {path:'/connexion', name:'connect', component:Connect}
  ]
});

export default router;