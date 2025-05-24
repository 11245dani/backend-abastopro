<template>
  <div class="forgot-password-page">
    <div v-if="toastVisible" class="toast success-toast">
  {{ mensaje }}
</div>
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
    error: '',
    toastVisible: false
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
  this.toastVisible = true
  setTimeout(() => {
    this.toastVisible = false
  }, 4000)
} catch (error) {
  this.error = error.response?.data?.mensaje || 'Error al enviar el enlace'
}
    }
  }
}
</script>

<style scoped>
.toast {
  position: fixed;
  top: 30px;
  right: 30px;
  padding: 15px 25px;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  z-index: 1000;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  animation: slideDown 0.5s ease, fadeOut 0.5s ease 3.5s;
}

.success-toast {
  background-color: #2ecc71;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeOut {
  to {
    opacity: 0;
    transform: translateY(-20px);
  }
}
</style>
