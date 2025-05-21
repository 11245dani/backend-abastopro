<template>

  <header class="header">
    <div class="logo-text">
      <img src="@/images/Logo.jpeg" alt="Logo AbastoPro" />
      AbastoPro
    </div>
    <div class="user-menu" @click="toggleMenu">
      <img src="@/images/user-icon.png" alt="Perfil" class="icon" />
      <div v-if="showMenu" class="dropdown">
        <ul>
          <li @click="irAMiPerfil">Mi perfil</li>
          <li @click="actualizarDatos">Actualizar datos</li>
          <li @click="cerrarSesion">Cerrar sesión</li>
        </ul>
      </div>
    </div>
  </header>

  <div class="catalogo-container">

    <!-- Notificación simple -->
    <div
      v-if="notificacion"
      :class="['notificacion', tipoNotificacion]"
    >
      {{ notificacion }}
    </div>

    <!-- Panel de Filtros -->
    <aside class="filtros">
      <h3>Filtros</h3>
      <div class="filtro-section">
        <label>Categorías</label>
        <div v-for="cat in categorias" :key="cat.id">
          <input type="radio" :value="cat.id" v-model="filtros.categoria_id" /> {{ cat.nombre }}
        </div>
      </div>

      <div class="filtro-section">
        <label>Marcas</label>
        <div v-for="marca in marcas" :key="marca.id">
          <input type="radio" :value="marca.id" v-model="filtros.marca_id" /> {{ marca.nombre }}
        </div>
      </div>

      <div class="filtro-section">
        <label>Precio</label>
        <input type="number" v-model="filtros.precio_min" placeholder="Mínimo" />
        <input type="number" v-model="filtros.precio_max" placeholder="Máximo" />
      </div>

      <div class="acciones">
        <button @click="obtenerProductos">Aplicar filtros</button>
        <button @click="limpiarFiltros">Limpiar</button>
      </div>
    </aside>

    <div>
      <!-- Componente del carrito -->
      <CarritoFlotante
        :productos="carrito"
        :visible="mostrarCarrito"
        @cerrar="mostrarCarrito = false"
      />
    </div>

    <!-- Catálogo de Productos -->
    <div class="catalogo">
      <h2>Catálogo de productos disponibles</h2>

      <div v-if="cargando" class="loading">Cargando productos...</div>
      <div v-else-if="productos.length === 0" class="no-data">No hay productos disponibles.</div>

      <div v-else class="productos-grid">
        <div v-for="prod in productos" :key="prod.id" class="producto-card">
          <img v-if="prod.imagen_url" :src="prod.imagen_url" class="producto-imagen" />
          <h3>{{ prod.nombre }}</h3>
          <p class="categoria">{{ prod.categoria }}</p>
          <div class="precio-stock">
            <p class="precio">$ {{ Number(prod.precio).toFixed(2) }}</p>
            <p class="stock">Stock: {{ prod.stock }}</p>
          </div>
          <div class="marca-distribuidor">
            <p><strong>Marca:</strong> {{ prod.marca }}</p>
            <p><strong>Distribuidor:</strong> {{ prod.distribuidor }}</p>
          </div>
          <p class="descripcion">{{ prod.descripcion }}</p>
          <div class="botones">
            <button @click="verDetalle(prod)">Ver</button>
            <button @click="agregarAlCarrito(prod)">Añadir</button>
        </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="productoSeleccionado" class="modal" @click.self="cerrarModal">
      <div class="modal-contenido">
        <span class="cerrar" @click="cerrarModal">&times;</span>
        <h3>{{ productoSeleccionado.nombre }}</h3>
        <img v-if="productoSeleccionado.imagen_url" :src="productoSeleccionado.imagen_url" class="modal-imagen" />
        <p><strong>Categoría:</strong> {{ productoSeleccionado.categoria }}</p>
        <p><strong>Precio:</strong> $ {{ Number(productoSeleccionado.precio).toFixed(2) }}</p>
        <p><strong>Stock:</strong> {{ productoSeleccionado.stock }}</p>
        <p><strong>Marca:</strong> {{ productoSeleccionado.marca }}</p>
        <p><strong>Distribuidor:</strong> {{ productoSeleccionado.distribuidor }}</p>
        <p><strong>Descripción:</strong> {{ productoSeleccionado.descripcion }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const productos = ref([])
const cargando = ref(true)
const productoSeleccionado = ref(null)
const carrito = ref(JSON.parse(localStorage.getItem('carrito')) || [])
const mostrarCarrito = ref(false)

const filtros = ref({
  busqueda: '',
  categoria_id: null,
  marca_id: null,
  precio_min: '',
  precio_max: ''
})

const categorias = ref([])
const marcas = ref([])
const showMenu = ref(false)

const notificacion = ref(null)
const tipoNotificacion = ref('') // 'exito' o 'error'

const mostrarNotificacion = (mensaje, tipo = 'exito') => {
  notificacion.value = mensaje
  tipoNotificacion.value = tipo
  setTimeout(() => {
    notificacion.value = null
    tipoNotificacion.value = ''
  }, 3000)
}

const guardarCarrito = () => {
  localStorage.setItem('carrito', JSON.stringify(carrito.value))
}

const agregarAlCarrito = async (producto) => {
  const token = localStorage.getItem('token')
  if (!token) {
    alert('Debes iniciar sesión para agregar productos al carrito')
    return
  }

  try {
    await axios.post('/api/carrito/agregar', {
      producto_id: producto.id,
      cantidad: 1
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    const existente = carrito.value.find(p => p.id === producto.id)

    if (existente) {
      existente.cantidad += 1
    } else {
      carrito.value.push({
        ...producto,
        cantidad: 1,
        precio: parseFloat(producto.precio)
      })
    }

    guardarCarrito()
    mostrarNotificacion('Producto agregado al carrito', 'exito')
  } catch (error) {
    console.error(error)
    alert(error.response?.data?.message || 'Error al agregar al carrito')
  }
}


const obtenerProductos = async () => {
  cargando.value = true
  try {
    const response = await axios.get('/api/productos-disponibles', {
      params: filtros.value
    })
    productos.value = response.data
  } catch (error) {
    console.error('Error al cargar productos:', error)
  } finally {
    cargando.value = false
  }
}

const obtenerFiltros = async () => {
  try {
    const [cat, mar] = await Promise.all([
      axios.get('/api/categorias'),
      axios.get('/api/marcas')
    ])
    categorias.value = cat.data
    marcas.value = mar.data
  } catch (error) {
    console.error('Error al cargar filtros:', error)
  }
}

const limpiarFiltros = () => {
  filtros.value = {
    busqueda: '',
    categoria_id: null,
    marca_id: null,
    precio_min: '',
    precio_max: ''
  }
  obtenerProductos()
}

const verDetalle = (producto) => {
  productoSeleccionado.value = producto
}

const cerrarModal = () => {
  productoSeleccionado.value = null
}

const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

const router = useRouter()

const irAMiPerfil = () => {
  router.push('/perfil')
}

const actualizarDatos = () => {
  router.push('/actualizar-datos')
}

const cerrarSesion = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('usuario')
  localStorage.removeItem('rol')
  router.push('/login')
}

onMounted(() => {
  obtenerFiltros()
  obtenerProductos()
})
</script>


<style scoped>
/* Estilos existentes */

.notificacion {
  position: fixed;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  z-index: 9999;
  color: white;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.notificacion.exito {
  background-color: #4caf50;
}

.notificacion.error {
  background-color: #f44336;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background-color: white;
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
}

.dropdown ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.dropdown li {
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
}

.dropdown li:hover {
  background-color: #f0f0f0;
}

.catalogo-container {
  display: flex;
  gap: 20px;
  padding: 30px;
}

.filtros {
  width: 250px;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background: #f8f8f8;
  height: fit-content;
}

.filtros h3 {
  margin-bottom: 10px;
}

.filtro-section {
  margin-bottom: 15px;
}

.filtro-section label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}

.filtros input[type="text"],
.filtros input[type="number"] {
  width: 100%;
  padding: 5px;
  margin-top: 5px;
  margin-bottom: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.acciones {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}

.acciones button {
  padding: 6px;
  background-color: #000;
  border: none;
  color: white;
  border-radius: 6px;
  cursor: pointer;
}

.acciones button:hover {
  background-color: #333;
}

.catalogo {
  flex-grow: 1;
}

h2 {
  font-size: 22px;
  margin-bottom: 20px;
}

.loading,
.no-data {
  font-size: 16px;
  color: #666;
  text-align: center;
}

.productos-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.producto-card {
  flex: 1 1 calc(25% - 20px);
  min-width: 200px;
  max-width: 250px;
  background: #fff;
  border-radius: 10px;
  border: 1px solid #ccc;
  padding: 15px;
  text-align: center;
}

.producto-imagen {
  width: 100px;
  height: 100px;
  object-fit: cover;
  margin-bottom: 10px;
  border-radius: 6px;
}

.categoria {
  font-size: 0.9em;
  color: #555;
  margin-bottom: 10px;
}

.precio-stock {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  margin-bottom: 10px;
}

.marca-distribuidor {
  font-size: 0.85em;
  margin-bottom: 10px;
  color: #555;
}

.descripcion {
  font-size: 0.85em;
  color: #444;
  min-height: 40px;
}

.botones {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.botones button {
  padding: 6px 10px;
  background-color: #000;
  border: none;
  color: white;
  border-radius: 5px;
  font-size: 0.85em;
  cursor: pointer;
}

.botones button:hover {
  background-color: #333;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-contenido {
  background: white;
  padding: 20px;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  position: relative;
}

.modal-imagen {
  width: 100%;
  max-height: 200px;
  object-fit: contain;
  margin-bottom: 15px;
  border-radius: 8px;
}

.cerrar {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  cursor: pointer;
  color: #333;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-active, .modal-fade-leave-active {
  transition: all 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Slider estilo */
input[type="range"] {
  width: 100%;
  accent-color: #000;
  margin-top: 10px;
}
</style>
