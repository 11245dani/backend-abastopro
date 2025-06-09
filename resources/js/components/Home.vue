<template>
  <div>
    <header>
      <div class="logo-text">
        <div class="logo-container">
          <span class="logo-name">ABASTOPRO</span>
        </div>
      </div>
      <nav>
        
        <a href="/Login">Iniciar sesión</a>
        <button class="btn-register" @click="goToRegister">REGISTRARSE</button>
      </nav>
    </header>

    <main class="main">
      <div class="main-text">
        <h1>Conectamos Tiendas con Distribuidores</h1>
        <p>
          Nuestra plataforma facilita la conexión entre
          tiendas y distribuidores, optimizando el proceso de pedidos y entregas.
        </p>
        <div class="btn-group">
          <button class="btn-shop">
            MÁS SOBRE NOSOTROS
          </button>
          <button class="btn-distributor">
            CÓMO FUNCIONA
          </button>
        </div>
      </div>
      <div class="main-image">
        <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
      </div>
    </main>

    <footer>
      <div class="logo-footer">
        <div class="logo-container">
          <img src="@/images/Logo.jpeg" alt="Logo footer" />
          <span class="logo-name">ABASTOPRO</span>
        </div>
      </div>
      <div>© 2025 AbastoPro. Todos los derechos reservados.</div>
    </footer>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goToRegister() {
  router.push('/registro')
}
</script>

<style scoped>

* {
  font-family: Arial, sans-serif;
}

body {
  margin: 0;
  font-family: Arial, sans-serif;
  background-color: #fff;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 40px;
  border-bottom: 1px solid #e0e0e0;
}

.logo-container {
  display: flex;
  align-items: center;
  position: relative;
  padding: 5px 10px;
}

.logo-container::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border: 2px solid #194f3c;
  border-radius: 6px;
  pointer-events: none;
}

.logo-name {
  text-transform: uppercase;
  font-weight: 700;
  margin-left: 12px;
  color: #194f3c;
  letter-spacing: 0.8px;
  font-size: 1.4rem;
}

nav {
  display: flex;
  align-items: center;
  gap: 30px;
}

nav a {
  text-decoration: none;
  color: #194f3c;
  font-size: 15px;
  font-weight: 500;
  letter-spacing: 0.3px;
}

.btn-register {
  padding: 12px 24px;
  border: none;
  background-color: #9dd5ac;
  color: #141312;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  transition: background-color 0.2s;
}

.btn-register:hover {
  background-color: #8bc798;
}

.main {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 80px 40px;
  flex-wrap: wrap;
}

.main-text {
  flex: 1;
  min-width: 300px;
  padding-right: 60px;
}

.main-text h1 {
  font-size: 2.5rem;
  margin-bottom: 24px;
  color: #194f3c;
  font-weight: 700;
  line-height: 1.3;
}

.main-text p {
  font-size: 1.1rem;
  color: #141312;
  margin-bottom: 40px;
  line-height: 1.7;
  font-weight: 400;
}

.btn-group {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.btn-shop, .btn-distributor {
  padding: 16px 32px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
  min-width: 260px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 52px;
  box-sizing: border-box;
}

.btn-shop {
  background-color: #9dd5ac;
  color: #141312;
  border: none;
}

.btn-shop:hover {
  background-color: #8bc798;
}

.btn-distributor {
  background-color: white;
  color: #333;
  border: 1px solid #e0e0e0;
}

.btn-distributor:hover {
  background-color: #f5f5f5;
}

.main-image {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.main-image img {
  width: 100%;
  max-width: 600px;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

footer {
  text-align: center;
  padding: 40px;
  border-top: 1px solid #e0e0e0;
  color: #666;
  font-size: 14px;
  margin-top: 40px;
}

footer .logo-footer {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 10px;
}

footer .logo-container {
  padding: 4px;
}

@media (max-width: 768px) {
  header {
    flex-direction: column;
    gap: 20px;
    padding: 20px;
  }
  
  nav {
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
  }
  
  .main {
    flex-direction: column;
    padding: 50px 20px;
  }
  
  .main-text {
    padding-right: 0;
    margin-bottom: 40px;
  }
  
  .btn-group {
    flex-direction: column;
    gap: 15px;
  }
  
  .btn-shop, .btn-distributor {
    width: 100%;
    min-width: auto;
    padding: 14px;
  }
  
  .main-image img {
    max-width: 100%;
  }
}
</style>