<template>
  <div class="dashboard">
    <!-- Header -->
    <header class="header">
      <div class="logo-text">
        <img src="@/images/logoname.png" alt="Logo AbastoPro" />
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
        <ul class="menu">
          <li @click="irADashboard">Mis productos</li>
          <li @click="irAPedidos">Pedidos</li>
          <li @click="irAInformes">Informes</li> <!-- NUEVO BOTÓN -->
        </ul>
      </aside>

      <!-- Contenido principal -->
      <main class="contenido">
        <div class="contenido-header">
          <h1 class="bienvenida">Bienvenido, {{ nombreUsuario }}</h1>
          <button class="btn-anadir" @click="añadirProducto">+ Añadir producto</button>
        </div>

        <!-- Contenedor de cajas lado a lado -->
        <div class="contenedor-cajas">
          <!-- Productos Activos -->
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

          <!-- Pedidos Nuevos -->
          <div class="contenedor-productos-activos reducido">
            <div class="info">
              <h4>Pedidos Nuevos</h4>
              <p class="subtexto">Pedidos pendientes por aceptar</p>
            </div>
            <div class="contenido">
              <div class="cantidad-mini">{{ totalPedidosNuevos }}</div>
              <button @click="irAPedidos" class="btn-ver-mini">Ver</button>
            </div>
          </div>
        </div>
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
const totalPedidosNuevos = ref(0);
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

const obtenerTotalPedidosNuevos = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get('http://localhost:8000/api/gestor/pedidos-nuevos', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    totalPedidosNuevos.value = response.data.total_nuevos;
  } catch (error) {
    console.error('Error al obtener pedidos nuevos:', error);
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

const irAPedidos = () => {
  router.push('/Pedidos');
};

const irAProductos = () => {
  router.push('/MisProductos');
};

const irAInformes = () => { // NUEVA FUNCIÓN
  router.push('/Informes');
};

onMounted(() => {
  obtenerTotalProductos();
  obtenerTotalPedidosNuevos();
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
  font-family: Arial, sans-serif;
}

.contenedor-productos-activos.reducido .subtexto {
  font-size: 12px;
  color: #666;
  margin: 4px 0 0 0;
  font-family: Arial, sans-serif;
}

.contenedor-productos-activos.reducido .contenido {
  text-align: right;
  font-family: Arial, sans-serif;
}

.cantidad-mini {
  font-size: 24px;
  font-weight: bold;
  color: #222;
  margin-bottom: 5px;
}

.btn-ver-mini {
  background-color: #99D7A9;
  color: black;
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
  font-family: Arial, sans-serif;
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
  font-family: Arial, sans-serif;
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
  background-color: #99D7A9;
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
  font-family: Arial, sans-serif;
}

.btn-anadir {
  background-color: #99D7A9;
  color: black;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-anadir:hover {
  background-color: #89c196;
}

/* NUEVO: Contenedor para las dos cajas lado a lado */
.contenedor-cajas {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.contenedor-productos-activos.reducido {
  flex: 1 1 300px;
  max-width: 400px;
}
</style>
