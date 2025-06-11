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
axios.defaults.baseURL = 'http://localhost:8000/api'

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
  background-color: #f4f6f8;
  margin: 0;
  padding: 0;
}

.forgot-password-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 2rem;
  position: relative;
}

.form-wrapper {
  max-width: 450px;
  width: 100%;
  animation: fadeInUp 0.6s ease;
}

.back-link {
  display: inline-block;
  margin-bottom: 1rem;
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  transition: color 0.3s;
}

.back-link:hover {
  color: #21618c;
  text-decoration: underline;
}

.form-container {
  background-color: #ffffff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.3s ease;
}

.form-container:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
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
  transition: border-color 0.3s, box-shadow 0.3s;
  background-color: #fdfdfd;
}

input[type="email"]:focus {
  border-color: #3498db;
  outline: none;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
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
  transition: background-color 0.3s, box-shadow 0.3s;
}

.submit-btn:hover {
  background-color: #99D7A9;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
}

.success-message {
  margin-top: 1rem;
  color: #2ecc71;
  font-weight: 500;
  animation: fadeIn 0.4s ease;
}

.error-message {
  margin-top: 1rem;
  color: #e74c3c;
  font-weight: 500;
  animation: fadeIn 0.4s ease;
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

/* MODAL general */
.modal-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  animation: fadeIn 0.3s ease;
}

.modal {
  background-color: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  max-width: 380px;
  width: 100%;
  text-align: center;
  animation: scaleUp 0.4s ease;
}

.modal h3 {
  font-size: 20px;
  margin-bottom: 1rem;
}

.modal p {
  font-size: 16px;
  color: #444;
  margin-bottom: 1.5rem;
}

.modal button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.modal button:hover {
  background-color: #2b7db4;
}

/* MODALES diferenciados */
.modal-success h3 {
  color: #27ae60;
}

.modal-error h3 {
  color: #e74c3c;
}

.modal-success button {
  background-color: #27ae60;
}

.modal-success button:hover {
  background-color: #1e8449;
}

.modal-error button {
  background-color: #e74c3c;
}

.modal-error button:hover {
  background-color: #c0392b;
}

/* Animaciones */
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

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(25px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleUp {
  from {
    transform: scale(0.9);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
</style>