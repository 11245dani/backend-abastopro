<template>
  <div class="perfil-container" v-if="usuario">
    <h2>👤 Mi Perfil</h2>

    <div class="perfil-info">
      <div class="info-item">
        <span class="label">Nombre:</span>
        <span>{{ usuario.nombre }}</span>
      </div>

      <div class="info-item">
        <span class="label">Correo:</span>
        <span>{{ usuario.correo }}</span>
      </div>

      <div class="info-item">
        <span class="label">Rol:</span>
        <span>{{ formatoRol(usuario.rol) }}</span>
      </div>

      <div class="info-item">
        <span class="label">Dirección:</span>
        <span>{{ usuario.direccion || 'No registrada' }}</span>
      </div>

      <div class="info-item">
        <span class="label">Teléfono:</span>
        <span>{{ usuario.telefono || 'No registrado' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const usuario = ref(null)

onMounted(async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('/api/me', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    usuario.value = response.data
  } catch (error) {
    console.error('Error al obtener los datos del perfil:', error)
  }
})

const formatoRol = (rol) => {
  switch (rol) {
    case 'gestor_despacho': return 'Gestor de Despacho'
    case 'tendero': return 'Tendero'
    case 'admin': return 'Administrador'
    default: return rol
  }
}
</script>

<style scoped>
.perfil-container {
  max-width: 600px;
  margin: 40px auto;
  padding: 30px;
  border-radius: 16px;
  background-color: #ffffff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  font-family: 'Segoe UI', sans-serif;
  color: #333;
  border-left: 6px solid #99D7A9;
}

.perfil-container h2 {
  text-align: center;
  margin-bottom: 30px;
  color: #2f4f4f;
  font-size: 1.8rem;
}

.perfil-info {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 12px 16px;
  border-radius: 8px;
  background-color: #f4fdf7;
  border-left: 4px solid #99D7A9;
  font-size: 1rem;
}

.label {
  font-weight: 600;
  color: #444;
}
</style>
