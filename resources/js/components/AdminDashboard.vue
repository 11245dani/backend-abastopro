<template>
  <div class="dashboard">
    <header class="header">
      <div class="logo-text">
        <img src="@/images/logoname.png" alt="Logo AbastoPro" />
      </div>
      <div class="user-menu" @click="toggleMenu">
        <img src="@/images/user-icon.png" alt="Perfil" class="icon" />
        <div v-if="showMenu" class="dropdown">
          <ul>
            <li @click="irAMiPerfil">Mi perfil</li>
            <li @click="cerrarSesion">Cerrar sesión</li>
          </ul>
        </div>
      </div>
    </header>

    <div class="admin-dashboard">
      <!-- Panel Lateral -->
      <div class="admin-panel">
        
        <ul class="menu">
          <li @click="irADashboard">Dashboard</li>
          <li @click="router.push('/admin/usuarios')">Todos los usuarios</li>
          <li @click="mostrarFormulario = 'gestion'">Gestionar Categorías y Marcas</li>
        </ul>
      </div>

      <!-- Contenido principal -->
      <main class="contenido">
        <h1>Bienvenido, {{ nombreUsuario }}</h1>

        <!-- Lista de usuarios -->
        <!-- Mostrar lista de usuarios si fue solicitada -->
        <div v-if="usuariosCargados">
          <h2>Lista de Usuarios</h2>
          <div v-if="usuarios.length">
            <ul>
              <li v-for="usuario in usuarios" :key="usuario.id">
                {{ usuario.nombre }} - {{ usuario.correo }} - {{ usuario.rol }} - {{ usuario.estado }}
              </li>
            </ul>
          </div>
          <div v-else>
            <p>No hay usuarios disponibles.</p>
          </div>
        </div>

        <!-- Gestión de categorías y marcas -->
        <div v-if="mostrarFormulario === 'gestion'" class="form-gestion">
          <h2>Gestionar Categorías y Marcas</h2>

          <!-- Categorías -->
          <div class="gestion-seccion">
            <h3>Categorías</h3>
            <div class="form-group">
              <label>Nueva Categoría</label>
              <input v-model="nuevaCategoria" placeholder="Ej: Lácteos" />
              <button @click="crearCategoria" class="btn-secondary">Crear</button>
            </div>
            <ul>
              <li v-for="cat in categorias" :key="cat.id">{{ cat.nombre }}</li>
            </ul>
          </div>

          <!-- Marcas -->
          <div class="gestion-seccion" style="margin-top: 2rem;">
            <h3>Marcas</h3>
            <div class="form-group">
              <label>Nueva Marca</label>
              <input v-model="nuevaMarca" placeholder="Ej: Alpina" />
              <button @click="crearMarca" class="btn-secondary">Crear</button>
            </div>
            <ul>
              <li v-for="marca in marcas" :key="marca.id">{{ marca.nombre }}</li>
            </ul>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const mostrarFormulario = ref(null)
const showMenu = ref(false)

const nuevaCategoria = ref('')
const nuevaMarca = ref('')
const categorias = ref([])
const marcas = ref([])

const usuarios = ref([])
const usuariosCargados = ref(false)

const usuarioData = JSON.parse(localStorage.getItem('usuario')) || {}
const nombreUsuario = usuarioData.nombre || 'Usuario'

// Funciones de menú
const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

const irAMiPerfil = () => {
  router.push('/perfil')
}

const cerrarSesion = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('usuario')
  localStorage.removeItem('rol')
  router.push('/login')
}

const irADashboard = () => {
  router.push('/dashboard')
}

// Gestión de categorías
const obtenerCategorias = async () => {
  const res = await axios.get('/api/categorias')
  categorias.value = res.data
}

const crearCategoria = async () => {
  if (!nuevaCategoria.value.trim()) return
  await axios.post('/api/categorias', { nombre: nuevaCategoria.value }, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  })
  nuevaCategoria.value = ''
  obtenerCategorias()
}

// Gestión de marcas
const obtenerMarcas = async () => {
  const res = await axios.get('/api/marcas')
  marcas.value = res.data
}

const crearMarca = async () => {
  if (!nuevaMarca.value.trim()) return
  await axios.post('/api/marcas', { nombre: nuevaMarca.value }, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  })
  nuevaMarca.value = ''
  obtenerMarcas()
}

// Gestión de usuarios
const listarUsuarios = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/usuarios', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
      },
    })
    usuarios.value = response.data.usuarios
  } catch (error) {
    console.error(error)
    alert('Error al obtener usuarios: ' + (error.response?.data?.mensaje || 'Error desconocido'))
    usuarios.value = []
  } finally {
    usuariosCargados.value = true
  }
}

onMounted(() => {
  obtenerCategorias()
  obtenerMarcas()
})
</script>


<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.header {
  display: flex;
  justify-content: space-between;
  padding: 15px 20px;
  background-color: white;
  color: black;
  align-items: center;
  border-bottom: 1px solid #ccc;
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

.user-menu {
  position: relative;
  cursor: pointer;
}

.user-menu .icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.dropdown {
  position: absolute;
  right: 0;
  top: 40px;
  background: white;
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 160px;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
  z-index: 10;
}

.dropdown ul {
  list-style: none;
  margin: 0;
  padding: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.dropdown li {
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
}

.dropdown li:hover {
  background-color: #99D7A9;
}

.admin-dashboard {
  display: flex;
  flex: 1;
  overflow: hidden;
}

.admin-panel {
  width: 220px;
  background-color: white;
  padding: 20px;
  border-right: 1px solid #ddd;
  height: calc(100vh - 70px);
  box-sizing: border-box;
}

.menu {
  list-style: none;
  padding: 0;
  margin-top: 20px;
  font-family: Arial, Helvetica, sans-serif;
}

.menu li {
  padding: 10px;
  cursor: pointer;
}

.menu li:hover {
  background-color: #ddd;
}

.contenido {
  flex: 1;
  padding: 30px;
  background-color: #fafafa;
  height: calc(100vh - 70px);
  overflow-y: auto;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}
</style>
