import { createApp } from 'vue';
import Home from './components/Home.vue';
import Registro from './components/Registro.vue'
import axios from 'axios';


createApp(Home).mount('#app');
createApp(Registro).mount('#app')
