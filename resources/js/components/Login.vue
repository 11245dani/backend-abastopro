<template>
  <div class="login-page">
    <a href="/" class="back-link">&larr; Volver al inicio</a>
    <div class="login-container">
      <div class="logo">
        <img src="@/images/Logo.jpeg" alt="Logo de la empresa" />
      </div>

      <h2>Iniciar sesión</h2>
      <p class="subtitle">Ingresa tus credenciales para acceder a tu cuenta</p>

      <form @submit.prevent="iniciarSesion">
        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" v-model="correo" placeholder="correo electrónico@gmail.com" required />
        </div>

        <div class="form-group">
    <label for="password">
      Contraseña <router-link to="/recuperar-contrasena" class="forgot">¿Olvidaste tu contraseña?</router-link>
      </label>

          <input type="password" id="password" v-model="password" placeholder="introduce acá tu contraseña" required />
        </div>

        <div class="form-group">
          <label>Tipo de cuenta</label>
          <div class="account-type">
            <button type="button" :class="{ active: rol === 'tendero' }" @click="rol = 'tendero'">Tienda</button>
            <button type="button" :class="{ active: rol === 'gestor_despacho' }" @click="rol = 'gestor_despacho'">Distribuidor</button>
            <button type="button" :class="{ active: rol === 'admin' }" @click="rol = 'admin'">Administración</button>
          </div>
        </div>

        <button type="submit" class="submit-btn">Iniciar sesión</button>
      </form>

      <div class="bottom-text">
        ¿No tienes una cuenta? <a href="/registro">Registrarse</a>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { RouterLink } from 'vue-router'
axios.defaults.baseURL = 'http://127.0.0.1:8000/api/login';



export default {
  name: 'Login',
  data() {
    return {
      correo: '',
      password: '',
      rol: 'tendero', // valor por defecto
    }
  },
  methods: {

async iniciarSesion() {
  try {


    // 2. Hacer login
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
    }
  }
}
</script>

<style scoped>
body, .login-page {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #f2f2f2;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

.login-container {
  background-color: #fff;
  padding: 30px 40px;
  border-radius: 15px;
  box-shadow: 0 0 20px rgba(0,0,0,0.1);
  width: 360px;
  text-align: center;
  position: relative;
}

.logo {
  margin-bottom: 20px;
}

.logo img {
  width: 135px;
  height: auto;
}

h2 {
  margin-bottom: 5px;
  font-size: 24px;
}

.subtitle {
  margin-top: 0;
  margin-bottom: 20px;
  color: #555;
  font-size: 14px;
}

input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 14px;
}

.form-group {
  text-align: left;
  margin-bottom: 10px;
}

.form-group label {
  display: block;
  font-size: 13px;
  margin-bottom: 5px;
}

.forgot {
  float: right;
  font-size: 12px;
  color: #007bff;
  text-decoration: none;
}

.account-type {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
}

.account-type button {
  flex: 1;
  margin: 0 5px;
  padding: 10px;
  border: 1px solid #ccc;
  background-color: #f9f9f9;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
}

.account-type button.active {
  background-color: #99D7A9;
  color: black;
}

.submit-btn {
  width: 100%;
  padding: 12px;
  background-color: #99D7A9;
  color:  black;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  cursor: pointer;
  margin-top: 10px;
}

.bottom-text {
  margin-top: 15px;
  font-size: 13px;
}

.bottom-text a {
  color: #007bff;
  text-decoration: none;
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
