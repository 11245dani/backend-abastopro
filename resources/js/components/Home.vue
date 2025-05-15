<template>
  <div>
    <header>
      <div class="logo-text">
        <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
        AbastoPro
      </div>
      <nav>
        <a href="/Login">Iniciar sesión</a>
        <button class="btn" @click="goRegister">Registrarse</button>
      </nav>
    </header>

    <main class="main">
      <div class="main-text">
        <h1>Conectamos tiendas con distribuidores</h1>
        <p>Nuestra plataforma facilita la conexión entre tiendas y distribuidores, optimizando el proceso de pedidos y entregas.</p>
        <div class="btn-group">
          <button class="btn">Más sobre nosotros</button>
          <button class="btn" style="background-color: #e0e0e0; color: #000;">Cómo funciona</button>
        </div>
      </div>
      <div class="main-image">
        <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
      </div>
    </main>

    <footer>
      <div class="logo-footer">
        <img src="@/images/Logo.jpeg" alt="Logo footer" />
        AbastoPro
      </div>
      <div>© 2025 AbastoPro. Todos los derechos reservados.</div>
    </footer>
  </div>
</template>

<script setup>
function goRegister() {
  window.location.href = '/registro';
}
</script>

<style scoped>
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
      border-bottom: 1px solid #ddd;
    }

    .logo-text {
      display: flex;
      align-items: center;
      font-weight: bold;
      font-size: 22px;
    }

    .logo-text img {
      height: 40px;
      margin-right: 10px;
    }

    nav a {
      margin: 0 15px;
      text-decoration: none;
      color: #000;
      font-size: 15px;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .btn {
      padding: 10px 18px;
      border: none;
      background-color: #000;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      font-size: 15px;
    }

    .main {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 60px 40px;
      flex-wrap: wrap;
    }

    .main-text {
      flex: 1;
      min-width: 300px;
      padding-right: 40px;
    }

    .main-text h1 {
      font-size: 34px;
      margin-bottom: 20px;
    }

    .main-text p {
      font-size: 18px;
      color: #555;
      margin-bottom: 30px;
      line-height: 1.6;
    }

    .main-text .btn-group {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
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
    }

    footer {
      text-align: center;
      padding: 30px;
      border-top: 1px solid #ddd;
      color: #555;
      font-size: 14px;
    }

    footer .logo-footer {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-weight: bold;
      margin-bottom: 5px;
    }

    footer .logo-footer img {
      height: 30px;
    }

    @media (max-width: 768px) {
      .main {
        flex-direction: column;
      }
      .main-text, .main-image {
        padding: 20px 0;
        max-width: 100%;
      }
    }
</style>
