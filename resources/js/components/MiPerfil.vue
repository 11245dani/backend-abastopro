<template>
  <div class="perfil-container" v-if="usuario">
    <h2>Mi Perfil</h2>
    <div class="perfil-info">
      <p><strong>Nombre:</strong> {{ usuario.nombre }}</p>
      <p><strong>Correo:</strong> {{ usuario.correo }}</p>
      <p><strong>Rol:</strong> {{ formatoRol(usuario.rol) }}</p>
      <p><strong>Dirección:</strong> {{ usuario.direccion || 'No registrada' }}</p>
      <p><strong>Teléfono:</strong> {{ usuario.telefono || 'No registrado' }}</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const usuario = ref(null);

onMounted(async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get('/api/me', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    usuario.value = response.data;
  } catch (error) {
    console.error('Error al obtener los datos del perfil:', error);
  }
});

const formatoRol = (rol) => {
  if (rol === 'gestor_despacho') return 'Gestor de Despacho';
  if (rol === 'tendero') return 'Tendero';
  if (rol === 'admin') return 'Administrador';
  return rol;
};
</script>

<style scoped>
.perfil-container {
  max-width: 500px;
  margin: 40px auto;
  padding: 30px;
  border: 1px solid #ccc;
  border-radius: 12px;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  font-family: Arial, sans-serif;
}

.perfil-container h2 {
  text-align: center;
  margin-bottom: 20px;
}

.perfil-info p {
  margin: 10px 0;
  font-size: 15px;
}
</style>
