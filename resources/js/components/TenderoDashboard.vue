<template>
  <div class="dashboard">
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

    <main class="contenido">
      <h1>Bienvenido, {{ nombreUsuario }}</h1>
      <!-- Aquí va el contenido del dashboard -->
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const showMenu = ref(false);
const router = useRouter();

// Recuperar los datos del usuario desde localStorage
const usuarioData = JSON.parse(localStorage.getItem('usuario')) || {};
const nombreUsuario = usuarioData.nombre || 'Usuario';

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
</script>

<style scoped>
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

.contenido {
  padding: 30px;
}
</style>
