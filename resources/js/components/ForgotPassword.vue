<template>
  <div class="forgot-password-page">
    <a href="/" class="back-link">&larr; Volver al inicio</a>
    <div class="form-container">
      <h2>Restablecer contraseña</h2>
      <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

      <form @submit.prevent="enviarEnlace">
        <div class="form-group">
          <label for="correo">Correo electrónico</label>
          <input type="email" v-model="correo" id="correo" required />
        </div>
        <button type="submit" class="submit-btn">Enviar enlace</button>
      </form>

      <p v-if="mensaje" class="success-message">{{ mensaje }}</p>
      <p v-if="error" class="error-message">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'

export default {
  name: 'ForgotPassword',
  data() {
    return {
      correo: '',
      mensaje: '',
      error: ''
    }
  },
  methods: {
    async enviarEnlace() {
      this.mensaje = ''
      this.error = ''
      try {
        const response = await axios.post('/forgot-password', {
          correo: this.correo
        }, {
          headers: { Accept: 'application/json' }
        })
        this.mensaje = response.data.mensaje
      } catch (error) {
        this.error = error.response?.data?.mensaje || 'Error al enviar el enlace'
      }
    }
  }
}
</script>

<style scoped>
/* Agrega tus estilos aquí */
</style>
