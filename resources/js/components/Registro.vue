<template>
  <div class="register-page">

    <a href="/" class="back-link">&larr; Volver al inicio</a>
    <div class="container">
      

      <div class="logo">
        <img src="@/images/Logo.jpeg" alt="Logo de la empresa" />
      </div>

      <div class="title">
        <h2>Crear una cuenta</h2>
        <p>Selecciona el tipo de cuenta que deseas crear</p>
      </div>

      <div class="account-type">
        <button :class="{ active: form.rol === 'tendero' }" @click="setRol('tendero')">Tienda</button>
        <button :class="{ active: form.rol === 'gestor_despacho' }" @click="setRol('gestor_despacho')">Distribuidor</button>
      </div>

      <form @submit.prevent="registrarUsuario">
        <input v-model="form.nombre" type="text" placeholder="Nombre" required />
        <input v-model="form.correo" type="email" placeholder="Correo electrónico" required />
        <input v-model="form.password" type="password" placeholder="Contraseña" required />
        <input v-model="form.telefono" type="text" placeholder="Télefono" required />


        <input
          v-if="form.rol === 'tendero'"
          v-model="form.direccion"
          type="text"
          placeholder="Dirección"
          required
        />

        <div v-if="form.rol === 'gestor_despacho'">
          <input v-model="form.nombre_empresa" type="text" placeholder="Nombre de la Empresa" required />
          <input v-model="form.direccion" type="text" placeholder="Dirección" required />

        </div>

        <button type="submit" class="submit-btn">Registrarse como {{ form.rol === 'tendero' ? 'Tienda' : 'Distribuidor' }}</button>

        <p v-if="mensaje" :style="{ color: mensajeColor }">{{ mensaje }}</p>
      </form>

      <div class="toggle">
        <p>¿Ya tienes una cuenta? <router-link to="/Login">Iniciar sesión</router-link></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const form = ref({
  nombre: '',
  correo: '',
  password: '',
  rol: 'tendero',
  direccion: '',
  nombre_empresa: '',
  telefono: ''
})

const mensaje = ref('')
const mensajeColor = ref('green')

function setRol(rol) {
  form.value.rol = rol
}

async function registrarUsuario() {
  mensaje.value = ''

  const payload = { ...form.value }

  if (payload.rol === 'tendero') {
    delete payload.nombre_empresa
  }

  try {
    const res = await axios.post('http://127.0.0.1:8000/api/register', payload, {
      headers: {
        Accept: 'application/json'
      }
    })

    mensaje.value = '¡Registro exitoso! Verifica tu correo electrónico.'
    mensajeColor.value = 'green'

    form.value = {
      nombre: '',
      correo: '',
      password: '',
      rol: 'tendero',
      direccion: '',
      nombre_empresa: '',
      telefono: ''
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const errores = Object.values(error.response.data.errors).flat().join(', ')
      mensaje.value = errores
    } else if (error.response?.data?.mensaje) {
      mensaje.value = error.response.data.mensaje
    } else {
      mensaje.value = 'Error desconocido'
    }
    mensajeColor.value = 'red'
  }
}

</script>

<style scoped>

.register-page {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #e7ffed;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f9f9f9;
}

.container {
  background-color: #fff;
  padding: 30px 40px;
  border-radius: 15px;
  box-shadow: 0 0 20px rgba(0,0,0,0.1);
  width: 360px;
  text-align: center;
  position: relative;
}

.logo {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.logo img {
  width: 120px;
  height: auto;
}

.title {
  text-align: center;
  margin-bottom: 20px;
  font-family: Arial, sans-serif;
}

.account-type {
  display: flex;
  justify-content: space-around;
  margin-bottom: 20px;
}

.account-type button {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  cursor: pointer;
  background-color: whitesmoke;
  font-weight: normal;
}

.account-type button.active {
  background-color: #99D7A9;
  border: 1px solid #ccc;
}

input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.submit-btn {
  background-color: #99D7A9;
  color: Black;
  border: none;
  border-radius: 5px;
  padding: 10px;
  cursor: pointer;
  width: 100%;
}

.toggle {
  text-align: center;
  margin-top: 20px;
  font-family: Arial, sans-serif;
}

.back-link {
  position: absolute;
  top: 15px;
  left: 20px;
  font-size: 13px;
  color: gray;
  text-decoration: none;
}
</style>
