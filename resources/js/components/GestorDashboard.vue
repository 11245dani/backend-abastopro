<template>
  <div class="dashboard">
    <!-- Header -->
    <header class="header">
      <div class="logo-text">
        <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
        AbastoPro
      </div>

      <div class="user-menu" @click="toggleMenu">
        <img src="@/images/user-icon.png" alt="Perfil" class="icon" />
        <div v-if="showMenu" class="dropdown">
          <ul>
            <li @click="irAMiPerfil">Mi perfil</li>
            <li @click="actualizarDatos">Actualizar datos</li>
            <li @click="cerrarSesion">Cerrar sesión</li>
          </ul>
        </div>
      </div>
  
    </header>

    <!-- Contenedor principal: panel lateral + contenido -->
    <div class="main-container">
      <!-- Panel lateral -->
      <aside class="Gestor-panel">
        <div class="logo-text">
          <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
        </div>
        <ul class="menu">
          <li @click="irADashboard">Mis productos</li>
        </ul>
      </aside>

      <!-- Contenido principal -->
      <main class="contenido">
        <div class="contenido-header">
          <h1 class="bienvenida">Bienvenido, {{ nombreUsuario }}</h1>

          <button class="btn-anadir" @click="añadirProducto">+ Añadir producto</button>
          
        </div>

 <!-- Contenedor de productos activos (más compacto) -->
<div class="contenedor-productos-activos reducido">
  <div class="info">
    <h4>Productos Activos</h4>
    <p class="subtexto">Productos disponibles para venta</p>
  </div>
  <div class="contenido">
    <div class="cantidad-mini">{{ totalProductos }}</div>
    <button @click="irAProductos" class="btn-ver-mini">Ver</button>
  </div>
</div>
        <!-- Aquí va el contenido adicional del dashboard -->
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';


const router = useRouter();
const totalProductos = ref(0);

const showMenu = ref(false);

const usuarioData = JSON.parse(localStorage.getItem('usuario')) || {};
const nombreUsuario = usuarioData.nombre || 'Usuario';

const obtenerTotalProductos = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get('http://localhost:8000/api/productos', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
  totalProductos.value = response.data.length;
  } catch (error) {
    console.error('Error al obtener el total de productos', error);
  }
};


const toggleMenu = () => {
  showMenu.value = !showMenu.value;
};

const irAMiPerfil = () => {
  router.push('/perfil');
};

const actualizarDatos = () => {
  router.push('/actualizar-datos');
};

const cerrarSesion = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('usuario');
  localStorage.removeItem('rol');
  router.push('/login');
};

const irADashboard = () => {
  router.push('/MisProductos');
};

const añadirProducto = () => {
  router.push('/CrearProducto');
};

const irAProductos = () => {
  router.push('/MisProductos') // ajusta la ruta según tu router
}

onMounted(() => {
console.log('GestorDashboard montado'); // <-- Confirmación
  obtenerTotalProductos();
});
</script>

<style scoped>

.contenedor-productos-activos.reducido {
  background: #f3f3f3;
  border-radius: 12px;
  padding: 15px 20px;
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 400px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.contenedor-productos-activos.reducido .info h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
  color: #111;
}

.contenedor-productos-activos.reducido .subtexto {
  font-size: 12px;
  color: #666;
  margin: 4px 0 0 0;
}

.contenedor-productos-activos.reducido .contenido {
  text-align: right;
}

.cantidad-mini {
  font-size: 24px;
  font-weight: bold;
  color: #222;
  margin-bottom: 5px;
}

.btn-ver-mini {
  background-color: #222;
  color: white;
  padding: 6px 12px;
  border: none;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-ver-mini:hover {
  background-color: #444;
}


/* Layout General */
.dashboard {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-container {
  display: flex;
  flex-grow: 1;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background-color: white;
}

.logo-text {
  display: flex;
  align-items: center;
  font-weight: bold;
  font-size: 22px;
}

.logo-text img {
  height: 40px;
  margin-right: 10px;
}

.user-menu {
  position: relative;
  cursor: pointer;
}

.user-menu .icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.dropdown {
  position: absolute;
  right: 0;
  top: 40px;
  background: white;
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 160px;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
}

.dropdown ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown li {
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
}

.dropdown li:hover {
  background-color: #f0f0f0;
}

/* Panel lateral */
.Gestor-panel {
  background-color: white;
  padding: 20px;
  width: 220px;
  border-right: 1px solid #ddd;
  box-sizing: border-box;
}

.menu {
  list-style: none;
  padding: 0;
  margin-top: 20px;
}

.menu li {
  padding: 10px;
  cursor: pointer;
}

.menu li:hover {
  background-color: #e6e6e6;
}

/* Contenido principal */
.contenido {
  padding: 30px;
  flex-grow: 1;
  box-sizing: border-box;
}

.contenido-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.bienvenida {
  font-size: 18px;
  font-weight: 500;
  margin: 0;
}

.btn-anadir {
  background-color: black;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-anadir:hover {
  background-color: #333;
}
</style>
