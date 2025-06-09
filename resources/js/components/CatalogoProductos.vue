<template>
  <!-- Header -->
  <header class="header">
    <div class="logo-text">
      <img src="@/images/logoname.png" alt="Logo AbastoPro" />
    </div>
    <div class="header-icons">
      <div class="cart-icon" @click="irACarrito">
        <div class="cart-wrapper">
          <img src="@/images/cart-icon.png" alt="Carrito" class="icon" />
          <span v-if="carrito.length > 0" class="cart-badge">{{ carrito.length }}</span>
        </div>
      </div>
      <div class="user-menu" @click="toggleMenu">
        <img src="@/images/user-icon.png" alt="Perfil" class="icon" />
        <div v-if="showMenu" class="dropdown">
          <ul>
            <li @click="irAMiPerfil">
              <i class="icon-user"></i>
              Mi perfil
            </li>
            <li @click="actualizarDatos">
              <i class="icon-edit"></i>
              Actualizar datos
            </li>
            <li @click="cerrarSesion">
              <i class="icon-logout"></i>
              Cerrar sesión
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <div class="catalogo-container">
    <!-- Notificación moderna -->
    <div v-if="notificacion" :class="['notificacion', tipoNotificacion]">
      <div class="notificacion-content">
        <span class="notificacion-icon">
          <i v-if="tipoNotificacion === 'exito'" class="icon-check"></i>
          <i v-else class="icon-error"></i>
        </span>
        {{ notificacion }}
      </div>
    </div>

    <!-- Panel de Filtros Mejorado -->
    <aside class="filtros">

      <!-- Búsqueda -->
      <div class="filtro-section">
        <label>
          <i class="icon-search"></i>
          Buscar producto
        </label>
        <div class="search-wrapper">
          <input 
            type="text" 
            v-model="filtros.busqueda" 
            placeholder="Nombre del producto..."
            @input="debounceSearch"
            class="search-input"
          />
        </div>
      </div>

      <!-- Categorías -->
      <div class="filtro-section">
        <label>
          <i class="icon-category"></i>
          Categorías
        </label>
        <div class="filter-options">
          <div class="filter-option">
            <input type="radio" :value="null" v-model="filtros.categoria_id" id="cat-todas" />
            <label for="cat-todas" class="filter-label">Todas las categorías</label>
          </div>
          <div v-for="cat in categorias" :key="cat.id" class="filter-option">
            <input type="radio" :value="cat.id" v-model="filtros.categoria_id" :id="`cat-${cat.id}`" />
            <label :for="`cat-${cat.id}`" class="filter-label">{{ cat.nombre }}</label>
          </div>
        </div>
      </div>

      <!-- Marcas -->
      <div class="filtro-section">
        <label>
          <i class="icon-brand"></i>
          Marcas
        </label>
        <div class="filter-options">
          <div class="filter-option">
            <input type="radio" :value="null" v-model="filtros.marca_id" id="marca-todas" />
            <label for="marca-todas" class="filter-label">Todas las marcas</label>
          </div>
          <div v-for="marca in marcas" :key="marca.id" class="filter-option">
            <input type="radio" :value="marca.id" v-model="filtros.marca_id" :id="`marca-${marca.id}`" />
            <label :for="`marca-${marca.id}`" class="filter-label">{{ marca.nombre }}</label>
          </div>
        </div>
      </div>

      <!-- Rango de Precio -->
      <div class="filtro-section">
        <label>
          <i class="icon-price"></i>
          Rango de precio
        </label>
        <div class="price-inputs">
          <div class="price-input-wrapper">
            <span class="currency">$</span>
            <input 
              type="number" 
              v-model="filtros.precio_min" 
              placeholder="Mínimo"
              class="price-input"
            />
          </div>
          <span class="price-separator">-</span>
          <div class="price-input-wrapper">
            <span class="currency">$</span>
            <input 
              type="number" 
              v-model="filtros.precio_max" 
              placeholder="Máximo"
              class="price-input"
            />
          </div>
        </div>
      </div>

      <!-- Botón de aplicar -->
      <div class="filtros-footer">
        <button @click="obtenerProductos" class="btn-aplicar">
          <i class="icon-check"></i>
          Aplicar filtros
        </button>
      </div>
    </aside>

    <!-- Carrito Flotante -->
    <div>
      <CarritoFlotante
        :productos="carrito"
        :visible="mostrarCarrito"
        @cerrar="mostrarCarrito = false"
      />
    </div>

    <!-- Catálogo Principal -->
    <main class="catalogo">
      <div class="catalogo-header">
        <h2>
          Catálogo de productos
        </h2>
      </div>

      <!-- Loading Estado -->
      <div v-if="cargando" class="loading-container">
        <div class="loading-spinner"></div>
        <p>Cargando productos...</p>
      </div>

      <!-- Sin productos -->
      <div v-else-if="productos.length === 0" class="no-data">
        <div class="no-data-icon">
          <i class="icon-empty"></i>
        </div>
        <h3>No hay productos disponibles</h3>
        <p>Intenta ajustar los filtros para encontrar más productos</p>
      </div>

      <!-- Grid de Productos -->
      <div v-else class="productos-grid">
        <div 
          v-for="prod in productos" 
          :key="prod.id" 
          class="producto-card"
          :class="{ 'sin-stock': prod.stock <= 0 }"
        >
          <div class="producto-imagen-container">
            <img 
              v-if="prod.imagen_url" 
              :src="prod.imagen_url" 
              class="producto-imagen"
              :alt="prod.nombre"
            />
            <div v-else class="imagen-placeholder">
              <i class="icon-image"></i>
            </div>
            <div v-if="prod.stock <= 0" class="sin-stock-overlay">
              Sin stock
            </div>
          </div>

          <div class="producto-info">
            <div class="producto-header">
              <h3 class="producto-nombre">{{ prod.nombre }}</h3>
              <span class="categoria-tag">{{ prod.categoria }}</span>
            </div>

            <div class="precio-stock-container">
              <div class="precio-info">
                <span class="precio">{{ formatearPrecio(prod.precio) }}</span>
              </div>
              <div class="stock-info" :class="{ 'stock-bajo': prod.stock <= 5, 'sin-stock': prod.stock <= 0 }">
                <i class="icon-stock"></i>
                <span>{{ prod.stock > 0 ? `${prod.stock} unidades` : 'Sin stock' }}</span>
              </div>
            </div>

            <div class="marca-distribuidor">
              <div class="marca-info">
                <i class="icon-brand"></i>
                <span>{{ prod.marca }}</span>
              </div>
              <div class="distribuidor-info">
                <i class="icon-distributor"></i>
                <span>{{ prod.distribuidor }}</span>
              </div>
            </div>

            <p class="descripcion">{{ prod.descripcion || 'Sin descripción disponible' }}</p>

            <div class="producto-acciones">
              <button @click="verDetalle(prod)" class="btn-ver">
                <i class="icon-eye"></i>
                Ver detalles
              </button>
              <button 
                @click="agregarAlCarrito(prod)" 
                class="btn-agregar"
                :disabled="prod.stock <= 0"
              >
                <i class="icon-cart-add"></i>
                {{ prod.stock <= 0 ? 'Sin stock' : 'Añadir' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Mejorado -->
    <div v-if="productoSeleccionado" class="modal-overlay" @click.self="cerrarModal">
      <div class="modal-contenido">
        <div class="modal-header">
          <h3>{{ productoSeleccionado.nombre }}</h3>
          <button class="btn-cerrar" @click="cerrarModal">
            <i class="icon-close"></i>
          </button>
        </div>

        <div class="modal-body">
          <div class="modal-imagen-container">
            <img 
              v-if="productoSeleccionado.imagen_url" 
              :src="productoSeleccionado.imagen_url" 
              class="modal-imagen"
              :alt="productoSeleccionado.nombre"
            />
            <div v-else class="modal-imagen-placeholder">
              <i class="icon-image"></i>
            </div>
          </div>

          <div class="modal-info">
            <div class="info-group">
              <label>Categoría:</label>
              <span class="categoria-tag">{{ productoSeleccionado.categoria }}</span>
            </div>

            <div class="info-group">
              <label>Precio:</label>
              <span class="precio-destacado">{{ formatearPrecio(productoSeleccionado.precio) }}</span>
            </div>

            <div class="info-group">
              <label>Stock disponible:</label>
              <span class="stock-info" :class="{ 'stock-bajo': productoSeleccionado.stock <= 5, 'sin-stock': productoSeleccionado.stock <= 0 }">
                {{ productoSeleccionado.stock > 0 ? `${productoSeleccionado.stock} unidades` : 'Sin stock' }}
              </span>
            </div>

            <div class="info-group">
              <label>Marca:</label>
              <span>{{ productoSeleccionado.marca }}</span>
            </div>

            <div class="info-group">
              <label>Distribuidor:</label>
              <span>{{ productoSeleccionado.distribuidor }}</span>
            </div>

            <div class="info-group descripcion-completa">
              <label>Descripción:</label>
              <p>{{ productoSeleccionado.descripcion || 'Sin descripción disponible' }}</p>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button 
            @click="agregarAlCarrito(productoSeleccionado)" 
            class="btn-agregar-modal"
            :disabled="productoSeleccionado.stock <= 0"
          >
            <i class="icon-cart-add"></i>
            {{ productoSeleccionado.stock <= 0 ? 'Sin stock' : 'Añadir al carrito' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
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
const tipoNotificacion = ref('')

let searchTimeout = null

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    obtenerProductos()
  }, 500)
}

// Watch para aplicar filtros automáticamente
watch([
  () => filtros.value.categoria_id,
  () => filtros.value.marca_id,
  () => filtros.value.precio_min,
  () => filtros.value.precio_max
], () => {
  obtenerProductos()
}, { deep: true })

const mostrarNotificacion = (mensaje, tipo = 'exito') => {
  notificacion.value = mensaje
  tipoNotificacion.value = tipo
  setTimeout(() => {
    notificacion.value = null
    tipoNotificacion.value = ''
  }, 4000)
}

const formatearPrecio = (precio) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(precio)
}

const guardarCarrito = () => {
  localStorage.setItem('carrito', JSON.stringify(carrito.value))
}

const agregarAlCarrito = async (producto) => {
  if (producto.stock <= 0) {
    mostrarNotificacion('Este producto no tiene stock disponible', 'error')
    return
  }

  const token = localStorage.getItem('token')
  if (!token) {
    mostrarNotificacion('Debes iniciar sesión para agregar productos al carrito', 'error')
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
        precio: parseFloat(producto.precio),
        imagen: producto.imagen_url
      })
    }

    guardarCarrito()
    mostrarNotificacion(`${producto.nombre} agregado al carrito`, 'exito')
    
    if (productoSeleccionado.value?.id === producto.id) {
      cerrarModal()
    }
  } catch (error) {
    console.error(error)
    mostrarNotificacion(error.response?.data?.message || 'Error al agregar al carrito', 'error')
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
    mostrarNotificacion('Error al cargar productos', 'error')
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
  showMenu.value = false
}

const actualizarDatos = () => {
  router.push('/actualizar-datos')
  showMenu.value = false
}

const irACarrito = () => {
  router.push('/CarritoVista')
}

const cerrarSesion = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('usuario')
  localStorage.removeItem('rol')
  localStorage.removeItem('carrito')
  router.push('/login')
}

onMounted(() => {
  obtenerFiltros()
  obtenerProductos()
})
</script>

<style scoped>

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background-color: var(--background-color);
  color: var(--text-primary);
  line-height: 1.6;
}

/* Iconos simplificados con CSS */
.icon-filter::before { content: '🔍'; }
.icon-reset::before { content: '↻'; }
.icon-search::before { content: '🔎'; }
.icon-category::before { content: '📂'; }
.icon-brand::before { content: '🏷️'; }
.icon-price::before { content: '💰'; }
.icon-check::before { content: '✓'; }
.icon-products::before { content: '📦'; }
.icon-empty::before { content: '📭'; }
.icon-image::before { content: '🖼️'; }
.icon-stock::before { content: '📊'; }
.icon-distributor::before { content: '🏢'; }
.icon-eye::before { content: '👁️'; }
.icon-cart-add::before { content: '🛒'; }
.icon-close::before { content: '✕'; }
.icon-user::before { content: '👤'; }
.icon-edit::before { content: '✏️'; }
.icon-logout::before { content: '🚪'; }
.icon-error::before { content: '⚠️'; }

/* Header mejorado */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background: var(--card-background);
  border-bottom: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
  position: sticky;
  top: 0;
  z-index: 100;
}

.logo-text {
  display: flex;
  align-items: center;
  font-weight: 700;
  font-size: 1.5rem;
  color: var(--primary-color);
}

.logo-text img {
  height: 2.5rem;
  margin-right: 0.75rem;
}

.header-icons {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.cart-wrapper {
  position: relative;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: var(--radius-md);
  transition: all 0.2s ease;
}

.cart-wrapper:hover {
  background-color: var(--background-color);
}

.cart-icon .icon {
  width: 1.5rem;
  height: 1.5rem;
}

.cart-badge {
  position: absolute;
  top: -0.25rem;
  right: -0.25rem;
  background: var(--error-color);
  color: black;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.125rem 0.375rem;
  border-radius: 9999px;
  min-width: 1.25rem;
  text-align: center;
}

.user-menu {
  position: relative;
}

.user-menu .icon {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid var(--border-color);
  transition: all 0.2s ease;
}

.user-menu .icon:hover {
  border-color: var(--primary-color);
}

.dropdown {
  position: absolute;
  right: 0;
  top: calc(100% + 0.5rem);
  background: var(--card-background);
  border: 1px solid var(--border-color);
  border-radius: var(--radius-lg);
  min-width: 12rem;
  box-shadow: var(--shadow-lg);
  z-index: 1000;
  overflow: hidden;
}

.dropdown ul {
  list-style: none;
  margin: 0;
  padding: 0.5rem 0;
}

.dropdown li {
  padding: 0.75rem 1rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

.dropdown li:hover {
  background-color: var(--background-color);
}

/* Notificación moderna */
.notificacion {
  position: fixed;
  top: 2rem;
  right: 2rem;
  z-index: 9999;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  animation: slideInRight 0.5s ease;
}

.notificacion.exito {
  background: var(--success-color);
  color: black;
}

.notificacion.error {
  background: var(--error-color);
  color: red;
}

.notificacion-content {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
}

.notificacion-icon {
  font-size: 1.25rem;
}

/* Layout principal */
.catalogo-container {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Panel de filtros mejorado */
.filtros {
  width: 320px;
  background: var(--card-background);
  border-radius: var(--radius-xl);
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
  height: fit-content;
  position: sticky;
  top: 6rem;
  overflow: hidden;
}

.filtros-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: white;
}

.filtros-header h3 {
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.125rem;
  font-weight: 600;
}

.btn-limpiar {
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: var(--radius-md);
  font-size: 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.btn-limpiar:hover {
  background: rgba(255, 255, 255, 0.3);
}

.filtro-section {
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.filtro-section:last-child {
  border-bottom: none;
}

.filtro-section label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  font-size: 0.875rem;
}

/* Búsqueda */
.search-wrapper {
  position: relative;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-lg);
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: var(--background-color);
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-color);
  background: white;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Filtros de radio */
.filter-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.filter-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.filter-option input[type="radio"] {
  width: 1rem;
  height: 1rem;
  accent-color: var(--primary-color);
}

.filter-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
  cursor: pointer;
  transition: color 0.2s ease;
}

.filter-option:hover .filter-label {
  color: var(--text-primary);
}

/* Inputs de precio */
.price-inputs {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.price-input-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.currency {
  position: absolute;
  left: 0.75rem;
  color: var(--text-secondary);
  font-size: 0.875rem;
  pointer-events: none;
}

.price-input {
  width: 100%;
  padding: 0.75rem 0.75rem 0.75rem 2rem;
  border: 2px solid var(--border-color);
  border-radius: var(--radius-md);
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.price-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.price-separator {
  color: var(--text-secondary);
  font-weight: 500;
}

/* Footer de filtros */
.filtros-footer {
  padding: 1.5rem;
}

.btn-aplicar {
  width: 100%;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 0.875rem 1rem;
  border-radius: var(--radius-lg);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-aplicar:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

/* Catálogo principal */
.catalogo {
  flex: 1;
  min-height: 100vh;
}

.catalogo-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.catalogo-header h2 {
  margin: 0;
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.resultados-info {
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 500;
}

/* Loading estado */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.loading-spinner {
  width: 3rem;
  height: 3rem;
  border: 3px solid var(--border-color);
  border-top: 3px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

.loading-container p {
  color: var(--text-secondary);
  font-size: 1rem;
  margin: 0;
}

/* Sin datos */
.no-data {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.no-data-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.no-data h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
  color: var(--text-primary);
}

.no-data p {
  margin: 0;
  color: var(--text-secondary);
  font-size: 1rem;
}

/* Grid de productos */
.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

/* Tarjeta de producto */
.producto-card {
  background: var(--card-background);
  border-radius: var(--radius-xl);
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  transition: all 0.3s ease;
  position: relative;
}

.producto-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-color);
}

.producto-card.sin-stock {
  opacity: 0.7;
}

.producto-imagen-container {
  position: relative;
  height: 200px;
  overflow: hidden;
  background: var(--background-color);
}

.producto-imagen {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.producto-card:hover .producto-imagen {
  transform: scale(1.05);
}

.imagen-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: var(--text-secondary);
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
}

.sin-stock-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1.125rem;
}

/* Información del producto */
.producto-info {
  padding: 1.5rem;
}

.producto-header {
  margin-bottom: 1rem;
}

.producto-nombre {
  margin: 0 0 0.5rem 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.categoria-tag {
  display: inline-block;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.precio-stock-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.precio-info .precio {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
}

.stock-info {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--success-color);
  background: rgba(16, 185, 129, 0.1);
  padding: 0.25rem 0.5rem;
  border-radius: var(--radius-md);
}

.stock-info.stock-bajo {
  color: var(--warning-color);
  background: rgba(245, 158, 11, 0.1);
}

.stock-info.sin-stock {
  color: var(--error-color);
  background: rgba(239, 68, 68, 0.1);
}

.marca-distribuidor {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  gap: 1rem;
}

.marca-info,
.distribuidor-info {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  color: var(--text-secondary);
  flex: 1;
}

.descripcion {
  font-size: 0.875rem;
  color: var(--text-secondary);
  line-height: 1.5;
  margin-bottom: 1.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 3.75rem;
}

/* Acciones del producto */
.producto-acciones {
  display: flex;
  gap: 0.75rem;
}

.btn-ver {
  flex: 1;
  background: transparent;
  border: 2px solid var(--border-color);
  color: var(--text-secondary);
  padding: 0.75rem 1rem;
  border-radius: var(--radius-lg);
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-ver:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
  background: rgba(37, 99, 235, 0.05);
}

.btn-agregar {
  flex: 2;
  background: var(--primary-color);
  border: 2px solid var(--primary-color);
  color: white;
  padding: 0.75rem 1rem;
  border-radius: var(--radius-lg);
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-agregar:hover:not(:disabled) {
  background: var(--primary-dark);
  border-color: var(--primary-dark);
  transform: translateY(-1px);
}

.btn-agregar:disabled {
  background: var(--text-secondary);
  border-color: var(--text-secondary);
  cursor: not-allowed;
  opacity: 0.6;
}

/* Modal mejorado */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 2rem;
  backdrop-filter: blur(4px);
  animation: fadeIn 0.2s ease;
}

.modal-contenido {
  background: var(--card-background);
  border-radius: var(--radius-xl);
  box-shadow: var(--shadow-lg);
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideInUp 0.3s ease;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--border-color);
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: white;
  border-radius: var(--radius-xl) var(--radius-xl) 0 0;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.btn-cerrar {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-cerrar:hover {
  background: rgba(255, 255, 255, 0.3);
}

.modal-body {
  padding: 2rem;
}

.modal-imagen-container {
  text-align: center;
  margin-bottom: 2rem;
}

.modal-imagen {
  max-width: 100%;
  max-height: 300px;
  object-fit: contain;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
}

.modal-imagen-placeholder {
  width: 200px;
  height: 200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 4rem;
  color: var(--text-secondary);
  background: var(--background-color);
  border-radius: var(--radius-lg);
}

.modal-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.info-group label {
  font-weight: 600;
  color: var(--text-primary);
  min-width: 120px;
}

.info-group.descripcion-completa {
  flex-direction: column;
  align-items: flex-start;
}

.info-group.descripcion-completa label {
  margin-bottom: 0.5rem;
}

.info-group.descripcion-completa p {
  margin: 0;
  line-height: 1.6;
  color: var(--text-secondary);
}

.precio-destacado {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
}

.modal-footer {
  padding: 1.5rem 2rem;
  border-top: 1px solid var(--border-color);
  background: var(--background-color);
  border-radius: 0 0 var(--radius-xl) var(--radius-xl);
}

.btn-agregar-modal {
  width: 100%;
  background: var(--primary-color);
  border: none;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: var(--radius-lg);
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.btn-agregar-modal:hover:not(:disabled) {
  background: var(--primary-dark);
  transform: translateY(-1px);
}

.btn-agregar-modal:disabled {
  background: var(--text-secondary);
  cursor: not-allowed;
  opacity: 0.6;
}

/* Animaciones */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 1200px) {
  .productos-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }
}

@media (max-width: 768px) {
  .catalogo-container {
    flex-direction: column;
    padding: 1rem;
    gap: 1rem;
  }
  
  .filtros {
    width: 100%;
    position: static;
  }
  
  .header {
    padding: 1rem;
  }
  
  .header-icons {
    gap: 0.5rem;
  }
  
  .productos-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
  }
  
  .producto-card {
    min-width: 0;
  }
  
  .catalogo-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .catalogo-header h2 {
    font-size: 1.5rem;
  }
  
  .modal-overlay {
    padding: 1rem;
  }
  
  .modal-body,
  .modal-footer {
    padding: 1.5rem;
  }
  
  .notificacion {
    top: 1rem;
    right: 1rem;
    left: 1rem;
  }
}

@media (max-width: 480px) {
  .productos-grid {
    grid-template-columns: 1fr;
  }
  
  .precio-stock-container,
  .marca-distribuidor {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .producto-acciones {
    flex-direction: column;
  }
  
  .filtros-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .price-inputs {
    flex-direction: column;
  }
}

/* Estados de hover para dispositivos táctiles */
@media (hover: none) {
  .producto-card:hover {
    transform: none;
  }
  
  .producto-card:hover .producto-imagen {
    transform: none;
  }
}

/* Mejoras de accesibilidad */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* Focus states mejorados */
button:focus-visible,
input:focus-visible {
  outline: 2px solid var(--primary-color);
  outline-offset: 2px;
}

/* Scrollbar personalizada */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: var(--background-color);
}

::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--text-secondary);
}

</style>