<template>
  <div class="dashboard">
    <!-- Header -->
<header class="header">
  <div class="logo-text">
    <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
    AbastoPro
  </div>
  <div class="header-icons">
    <div class="cart-icon" @click="irACarrito">
      <img src="@/images/cart-icon.png" alt="Carrito" class="icon" />
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
  </div>
</header>


    <!-- Contenedor principal: panel lateral + contenido -->
    <div class="main-container">
      <!-- Panel lateral -->
      <aside class="Tendero-panel">
        <div class="logo-text">
          <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
        </div>
        <ul class="menu">
          <li @click="irAProductos">Productos</li>
          <li @click="irAMisPedidos">Mis pedidos</li>

        </ul>
      </aside>

      <!-- Contenido principal -->
      <main class="contenido">
        <div class="contenido-header">
          <h1 class="bienvenida">Bienvenido, {{ nombreUsuario }}</h1>
          <button class="btn-ver" @click="verCatalogo">Ver catálogo</button>
        </div>
        <!-- Aquí va el contenido adicional del dashboard -->
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const showMenu = ref(false);

const usuarioData = JSON.parse(localStorage.getItem('usuario')) || {};
const nombreUsuario = usuarioData.nombre || 'Usuario';

const toggleMenu = () => {
  showMenu.value = !showMenu.value;
};

const irACarrito = () => {
  router.push('/CarritoVista');
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

const irAProductos = () => {
  router.push('/catalogo'); // Ajusta según tu ruta real
};

const verCatalogo = () => {
  router.push('/catalogo'); // Ajusta según tu ruta real
};

const irAMisPedidos = () => {
  router.push('/mis-pedidos'); // Ajusta esta ruta según como tengas configurado el router
};

</script>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-container {
  display: flex;
  flex-grow: 1;
}

/* Panel lateral */
.Tendero-panel {
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

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  padding: 15px 20px;
  background-color: white;
  color: black;
  align-items: center;
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

.header-icons {
  display: flex;
  align-items: center;
  gap: 15px; /* Espacio entre los íconos */
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

.cart-icon .icon {
  width: 28px;
  height: 28px;
  cursor: pointer;
}

.cart-icon .icon:hover {
  filter: brightness(0.8);
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

/* Contenido */
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

.btn-ver {
  background-color: black;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-ver:hover {
  background-color: #333;
}
</style>
