<template>
  <div class="forgot-password-page">
    <div v-if="toastVisible" class="toast success-toast">{{ mensaje }}</div>

    <div class="form-wrapper">
      <a href="/" class="back-link">&larr; Volver al inicio</a>

      <div class="form-container">
        <h2>Restablecer contraseña</h2>
        <p class="subtext">
          Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <form @submit.prevent="enviarEnlace">
          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input
              type="email"
              v-model="correo"
              id="correo"
              required
              placeholder="tucorreo@ejemplo.com"
            />
          </div>

          <button type="submit" class="submit-btn">Enviar enlace</button>
        </form>

        <p v-if="mensaje" class="success-message">{{ mensaje }}</p>
        <p v-if="error" class="error-message">{{ error }}</p>
      </div>
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
body {
  font-family: 'Segoe UI', sans-serif;
}

.forgot-password-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f4f6f8;
  padding: 2rem;
}

.form-wrapper {
  max-width: 450px;
  width: 100%;
}

.back-link {
  display: block;
  margin-bottom: 1rem;
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
}

.form-container {
  background-color: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
}

h2 {
  margin-bottom: 0.5rem;
  font-size: 24px;
  font-weight: 600;
  color: #333;
}

.subtext {
  font-size: 14px;
  color: #666;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.2rem;
}

label {
  display: block;
  margin-bottom: 0.3rem;
  font-weight: 500;
  color: #555;
}

input[type="email"] {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  transition: border-color 0.3s;
}

input[type="email"]:focus {
  border-color: #3498db;
  outline: none;
}

.submit-btn {
  width: 100%;
  background-color: #99d7a9b5;
  color: black;
  border: 1px solid #ccc;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-btn:hover {
  background-color: #99D7A9;
}

.success-message {
  margin-top: 1rem;
  color: #2ecc71;
  font-weight: 500;
}

.error-message {
  margin-top: 1rem;
  color: #e74c3c;
  font-weight: 500;
}

/* Toast */
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
  animation: slideDown 0.5s ease, fadeOut 0.5s ease 3.5s forwards;
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