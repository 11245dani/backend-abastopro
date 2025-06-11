<template>
  <div class="login-page">
    <button class="back-button" @click="$router.push('/')">Volver al inicio</button>
    
    <div class="login-container">
      <!-- Sección izquierda con logo -->
      <div class="logo-section">
        <div class="logo-container">
          <img src="@/images/Logo.jpeg" alt="Logo ABASTOPRO" class="logo-img" />
        </div>
      </div>

      <!-- Sección derecha con formulario -->
      <div class="form-section">
        <h2>Iniciar sesión</h2>
        <p class="subtitle">Ingresa tus credenciales para acceder a tu cuenta</p>

        <form @submit.prevent="iniciarSesion">
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input 
              type="email" 
              id="email" 
              v-model="correo" 
              placeholder="email@ejemplo.com" 
              required 
            />
          </div>

          <div class="form-group">
            <label for="password">
              Contraseña 
              <router-link to="/recuperar-contrasena" class="forgot">¿Olvidaste tu contraseña?</router-link>
            </label>
            <input 
              type="password" 
              id="password" 
              v-model="password" 
              placeholder="introduce acá tu contraseña"
              required 
            />
          </div>

          <div class="form-group">
            <label>Tipo de cuenta</label>
            <div class="account-type">
              <button 
                type="button" 
                :class="{ active: rol === 'tendero' }" 
                @click="rol = 'tendero'"
              >
                Tienda
              </button>
              <button 
                type="button" 
                :class="{ active: rol === 'gestor_despacho' }" 
                @click="rol = 'gestor_despacho'"
              >
                Distribuidor
              </button>
              <button 
                type="button" 
                :class="{ active: rol === 'admin' }" 
                @click="rol = 'admin'"
              >
                Administración
              </button>
            </div>
          </div>

          <button type="submit" class="submit-btn">Iniciar sesión</button>
        </form>

        <div class="bottom-text">
          ¿No tienes una cuenta? <span class="register" @click="irARegistro">Registrarse</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      correo: '',
      password: '',
      rol: 'tendero',
    }
  },
  methods: {
    async iniciarSesion() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          correo: this.correo,
          password: this.password,
        }, {
          headers: {
            Accept: 'application/json'
          }
        });

        const { token, usuario, rol } = response.data;

        if (this.rol !== rol) {
          alert(`Tu cuenta está registrada como ${this.formatoRol(rol)}. Por favor selecciona el rol correcto.`);
          return;
        }

        localStorage.setItem('token', token);
        localStorage.setItem('usuario', JSON.stringify(usuario));

        if (rol === 'tendero') {
          this.$router.push('/tendero');
        } else if (rol === 'gestor_despacho') {
          this.$router.push('/gestor');
        } else if (rol === 'admin') {
          this.$router.push('/admin');
        }
      } catch (error) {
        alert(error.response?.data?.mensaje || 'Error al iniciar sesión');
      }
    },
    formatoRol(rol) {
      if (rol === 'admin') return 'Administrador';
      if (rol === 'tendero') return 'Tienda';
      if (rol === 'gestor_despacho') return 'Distribuidor';
      return rol;
    },
    irARegistro() {
      this.$router.push('/registro');
    }
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

.login-page {
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
  background-color: #7da97d;
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

.login-container {
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
  background-color:#7da97d;
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

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 15px;
  font-weight: 500;
  color: #333;
  margin-bottom: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.forgot {
  font-size: 12px;
  color: #7da97d;
  text-decoration: none;
  font-weight: 400;
  transition: color 0.2s;
}

.forgot:hover {
  color: #7da97d;
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
  border-color: #7da97d;
}

.account-type {
  display: flex;
  gap: 10px;
  margin-top: 10px;
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
  background-color: #7da97d;
  color: #141312;
  border-color: #7da97d;
}

.submit-btn {
  width: 100%;
  padding: 14px;
  background-color: #7da97d;
  color: #141312;
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

.register {
  color: #7da97d;
  text-decoration: underline;
  cursor: pointer;
  font-weight: 500;
  transition: color 0.2s;
}

.register:hover {
  color: #7da97d;
}

/* Responsive */
@media (max-width: 768px) {
  .login-container {
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

  .account-type {
    flex-direction: column;
    gap: 8px;
  }
  
  .form-group label {
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
  }
}
</style>