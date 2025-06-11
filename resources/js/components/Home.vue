<template>
  <div class="page-container">
    <header class="header">
      <div class="logo-text">
        <img src="@/images/logoname.png" alt="Logo AbastoPro" class="logo-image" />
      </div>
      <nav class="nav">
        <button class="btn-login" @click="goToLogin">INICIAR SESIÓN</button>
        <button class="btn-register" @click="goToRegister">REGISTRARSE</button>
      </nav>
    </header>

    <main class="main">
      <div class="main-content">
        <div class="main-text">
          <h1 class="main-title">Conectamos Tiendas con Distribuidores</h1>
          <p class="main-description">
            Nuestra plataforma facilita la conexión entre
            tiendas y distribuidores, optimizando el proceso de pedidos y entregas.
          </p>
          <div class="btn-group">
            <button class="btn-shop" @click="goToAbout">
              MÁS SOBRE NOSOTROS
            </button>
            <button class="btn-distributor" @click="goToHowItWorks">
              CÓMO FUNCIONA
            </button>
          </div>
        </div>
        <div class="main-image">
          <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" class="hero-image" />
        </div>
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

function goToLogin() {
  router.push('/Login')
}

function goToAbout() {
  router.push('/about')
}

function goToHowItWorks() {
  router.push('/how-it-works')
}
</script>

<style scoped>
/* Variables de color */
:root {
  --primary-color: #194f3c;
  --secondary-color: #9dd5ac;
  --secondary-hover: #8bc798;
  --text-dark: #141312;
  --text-light: #666;
  --border-color: #e0e0e0;
  --bg-light: #f5f5f5;
  --white: #fff;
}

/* Reset y estilos base */
.page-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  line-height: 1.6;
  color: var(--text-dark);
  background-color: var(--white);
  overflow-x: hidden;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2.5rem;
  border-bottom: 1px solid var(--border-color);
  background-color: var(--white);
  position: sticky;
  top: 0;
  z-index: 100;
  animation: fadeInDown 0.8s ease-out;
}

.logo-container {
  display: flex;
  align-items: center;
  position: relative;
  padding: 0.5rem 1rem;
}

.logo-container::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border: 2px solid var(--primary-color);
  border-radius: 6px;
  pointer-events: none;
  transition: all 0.3s ease;
}

.logo-container:hover::before {
  transform: scale(1.05);
}

.logo-image {
  height: 40px;
  width: auto;
  object-fit: contain;
}

.logo-name {
  text-transform: uppercase;
  font-weight: 700;
  margin-left: 0.75rem;
  color: var(--primary-color);
  letter-spacing: 0.05em;
  font-size: 1.4rem;
  transition: color 0.3s ease;
}

/* Navegación */
.nav {
  display: flex;
  align-items: center;
  gap: 1.875rem;
}

.nav-link {
  text-decoration: none;
  color: var(--primary-color);
  font-size: 0.9375rem;
  font-weight: 500;
  letter-spacing: 0.01875rem;
  position: relative;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background-color: var(--primary-color);
  transition: width 0.3s ease;
}

.nav-link:hover {
  color: #7da97d;
}

.nav-link:hover::after {
  width: 100%;
}

.btn-register, .btn-login {
  padding: 0.75rem 1.5rem;
  border: none;
  background-color: var(--secondary-color);
  color: var(--text-dark);
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-register:hover, .btn-login:hover {
  background-color: #7da97d;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Contenido principal */
.main {
  flex: 1;
  padding: 5rem 2.5rem;
  display: flex;
  align-items: center;
}

.main-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
  gap: 2rem;
}

.main-text {
  flex: 1;
  min-width: 300px;
  padding-right: 3.75rem;
  animation: fadeInLeft 0.8s ease-out;
}

.main-title {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
  color: #184e3c;
  font-weight: 700;
  line-height: 1.3;
  position: relative;
}

.main-title::after {
  content: '';
  display: block;
  width: 80px;
  height: 4px;
  background: var(--secondary-color);
  margin-top: 1rem;
}

.main-description {
  font-size: 1.1rem;
  color: var(--text-dark);
  margin-bottom: 2.5rem;
  line-height: 1.7;
  font-weight: 400;
  max-width: 500px;
}

.btn-group {
  display: flex;
  gap: 1.25rem;
  flex-wrap: wrap;
}

.btn-shop, .btn-distributor {
  padding: 1rem 2rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9375rem;
  min-width: 260px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 52px;
  box-sizing: border-box;
}

.btn-shop {
  background-color: var(--secondary-color);
  color: var(--text-dark);
  border: none;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-shop:hover {
  background-color: #7da97d;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-distributor {
  background-color: var(--white);
  color: var(--text-dark);
  border: 1px solid var(--border-color);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.btn-distributor:hover {
  background-color:#7da97d;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-color: var(--secondary-color);
}

.main-image {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeInRight 0.8s ease-out;
}

.hero-image {
  width: 100%;
  max-width: 600px;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  transition: transform 0.5s ease, box-shadow 0.5s ease;
}

.hero-image:hover {
  transform: scale(1.02);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
}

/* Footer */
.footer {
  text-align: center;
  padding: 2.5rem;
  border-top: 1px solid var(--border-color);
  color: var(--text-light);
  font-size: 0.875rem;
  margin-top: auto;
  background-color: var(--white);
  animation: fadeInUp 0.8s ease-out;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
}

.logo-footer {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-bottom: 0.625rem;
}

.footer-logo {
  width: 40px;
  height: auto;
}

.copyright {
  margin-top: 1rem;
}

/* Animaciones */
@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .header {
    flex-direction: column;
    gap: 1.25rem;
    padding: 1.25rem;
  }
  
  .nav {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.9375rem;
  }
  
  .main {
    flex-direction: column;
    padding: 3.125rem 1.25rem;
  }
  
  .main-content {
    flex-direction: column;
  }
  
  .main-text {
    padding-right: 0;
    margin-bottom: 2.5rem;
    text-align: center;
  }
  
  .main-title::after {
    margin-left: auto;
    margin-right: auto;
  }
  
  .main-description {
    margin-left: auto;
    margin-right: auto;
  }
  
  .btn-group {
    flex-direction: column;
    gap: 0.9375rem;
    align-items: center;
  }
  
  .btn-shop, .btn-distributor {
    width: 100%;
    min-width: auto;
    padding: 0.875rem;
  }
  
  .hero-image {
    max-width: 100%;
  }
  
  .logo-image {
    height: 35px;
  }
}
</style>