<template>
  <div class="register-page">
    <button class="back-button" @click="$router.push('/')">Volver al inicio</button>
    
    <div class="register-container">
      <div class="logo-section">
        <div class="logo-container">
          <img src="@/images/Logo.jpeg" alt="Logo ABASTOPRO" class="logo-img" />
        </div>
      </div>

      <div class="form-section">
        <h2>Crear una cuenta</h2>
        <p class="subtitle">Selecciona el tipo de cuenta que deseas crear</p>

        <form @submit.prevent="registrarUsuario">
          <div class="account-type">
            <button 
              type="button"
              :class="{ active: form.rol === 'tendero' }" 
              @click="setRol('tendero')"
            >
              Tienda
            </button>
            <button 
              type="button"
              :class="{ active: form.rol === 'gestor_despacho' }" 
              @click="setRol('gestor_despacho')"
            >
              Distribuidor
            </button>
          </div>

          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input 
              type="text" 
              id="nombre" 
              v-model="form.nombre" 
              placeholder="Tu nombre completo" 
              required 
            />
          </div>

          <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input 
              type="email" 
              id="correo" 
              v-model="form.correo" 
              placeholder="email@ejemplo.com" 
              required 
            />
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <input 
              type="password" 
              id="password" 
              v-model="form.password" 
              placeholder="Crea una contraseña segura"
              required 
            />
          </div>

          <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input 
              type="text" 
              id="telefono" 
              v-model="form.telefono" 
              placeholder="Número de contacto" 
              required 
            />
          </div>

          
          <div class="form-group" v-if="form.rol === 'tendero'">
            <label for="direccion">Dirección de la tienda</label>
            <input 
              type="text" 
              id="direccion" 
              v-model="form.direccion" 
              placeholder="Dirección de la tienda" 
              required 
            />
          </div>

          
          <template v-if="form.rol === 'gestor_despacho'">
            <div class="form-group">
              <label for="nombre_empresa">Nombre de la Empresa</label>
              <input 
                type="text" 
                id="nombre_empresa" 
                v-model="form.nombre_empresa" 
                placeholder="Nombre legal de la empresa" 
                required 
              />
            </div>

            <div class="form-group">
              <label for="direccion">Dirección de la empresa</label>
              <input 
                type="text" 
                id="direccion" 
                v-model="form.direccion" 
                placeholder="Dirección de la empresa" 
                required 
              />
            </div>
          </template>

          <button type="submit" class="submit-btn">
            Registrarse como {{ form.rol === 'tendero' ? 'Tienda' : 'Distribuidor' }}
          </button>

          <div v-if="mensaje" class="message" :class="{ error: mensajeColor === 'red' }">
            {{ mensaje }}
          </div>
        </form>

        <div class="bottom-text">
          ¿Ya tienes una cuenta? <span class="login-link" @click="$router.push('/login')">Iniciar sesión</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

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

    
    setTimeout(() => {
      router.push('/login')
    }, 2000)

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
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Arial, sans-serif;
}

.register-page {
  background-color: #FFFFFF;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.back-button {
  position: absolute;
  top: 30px;
  left: 30px;
  padding: 10px 20px;
  background-color: #8FBC8F;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.back-button:hover {
  background-color: #7da97d;
}

.register-container {
  display: flex;
  width: 80%;
  max-width: 1000px;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.logo-section {
  flex: 1;
  background-color: #8FBC8F;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

.logo-container {
  width: 250px;
  height: 250px;
  background-color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.logo-img {
  width: 200px;
  height: 200px;
  object-fit: contain;
}

.form-section {
  flex: 1;
  padding: 50px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

h2 {
  font-size: 28px;
  color: #333;
  margin-bottom: 10px;
  font-weight: 600;
}

.subtitle {
  color: #666;
  font-size: 15px;
  margin-bottom: 30px;
}

.account-type {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.account-type button {
  flex: 1;
  padding: 12px;
  border: 1px solid #ddd;
  background-color: white;
  border-radius: 5px;
  cursor: pointer;
  font-size: 15px;
  color: #333;
  transition: all 0.2s;
}

.account-type button.active {
  background-color: #8FBC8F;
  color: white;
  border-color: #8FBC8F;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 15px;
  font-weight: 500;
  color: #333;
  margin-bottom: 8px;
}

input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 15px;
  transition: border-color 0.2s;
}

input:focus {
  outline: none;
  border-color: #8FBC8F;
}

.submit-btn {
  width: 100%;
  padding: 14px;
  background-color: #8FBC8F;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  margin-top: 20px;
  transition: background-color 0.2s;
}

.submit-btn:hover {
  background-color: #7da97d;
}

.bottom-text {
  margin-top: 25px;
  font-size: 15px;
  text-align: center;
  color: #666;
}

.login-link {
  color: #8FBC8F;
  text-decoration: underline;
  cursor: pointer;
  font-weight: 500;
  transition: color 0.2s;
}

.login-link:hover {
  color: #7da97d;
}

.message {
  margin-top: 20px;
  padding: 12px;
  border-radius: 5px;
  text-align: center;
  font-size: 14px;
  background-color: #e6f7e6;
  color: #2e7d32;
}

.message.error {
  background-color: #ffebee;
  color: #c62828;
}

/* Responsive */
@media (max-width: 768px) {
  .register-container {
    flex-direction: column;
    width: 90%;
  }
  
  .logo-section {
    padding: 30px;
  }
  
  .logo-container {
    width: 180px;
    height: 180px;
  }
  
  .logo-img {
    width: 150px;
    height: 150px;
  }
  
  .form-section {
    padding: 40px;
  }
  
  .back-button {
    top: 20px;
    left: 20px;
    padding: 8px 16px;
    font-size: 14px;
  }
}
</style>