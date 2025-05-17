import { createRouter, createWebHistory } from 'vue-router'
import Home from './components/Home.vue'
import Registro from './components/Registro.vue'
import Login from './components/Login.vue'
import AdminDashboard from './components/AdminDashboard.vue';
import TenderoDashboard from './components/TenderoDashboard.vue';
import GestorDashboard from './components/GestorDashboard.vue';
import MiPerfil from './components/MiPerfil.vue';
import ListarUsuarios from './components/ListarUsuarios.vue'; // Asegúrate del path
import ActualizarDatos from './components/ActualizarDatos.vue'; // Asegúrate del path
import CrearProducto from './components/CrearProducto.vue';
import MisProductos from './components/MisProductos.vue';
import CatalogoProductos from './components/CatalogoProductos.vue';



const routes = [
  { path: '/', component: Home },
  { path: '/registro', component: Registro },
  { path: '/login', component: Login },
  { path: '/admin/usuarios', component: ListarUsuarios },
  { path: '/actualizar-datos', component: ActualizarDatos },
  { path: '/CrearProducto', component: CrearProducto },
  {path: '/MisProductos', component: MisProductos},

    {
    path: '/catalogo',
    name: 'CatalogoProductos',
    component: CatalogoProductos
  },


   {
    path: '/admin',
    name: 'Admin',
    component: AdminDashboard
  },
    {
    path: '/tendero',
    name: 'Tendero',
    component: TenderoDashboard
  },
  {
    path: '/gestor',
    name: 'Gestor',
    component: GestorDashboard
  },

    {
    path: '/perfil',
    name: 'Perfil',
    component: MiPerfil
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router

 