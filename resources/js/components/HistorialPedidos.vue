<template>
  <div class="historial-container">
    <div class="header">
      <h1 class="title">Mis Pedidos</h1>
      <button 
        @click="cargarHistorial" 
        class="refresh-btn"
        :class="{ 'loading': loading }"
      >
        <svg class="refresh-icon" viewBox="0 0 24 24">
          <path d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/>
        </svg>
      </button>
    </div>

    <transition name="fade" mode="out-in">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando tu historial...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <svg class="error-icon" viewBox="0 0 24 24">
          <path d="M12,2C6.48,2 2,6.48 2,12s4.48,10 10,10 10-4.48,10-10S17.52,2 12,2zm1,15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <p>{{ error }}</p>
        <button @click="cargarHistorial" class="retry-btn">Reintentar</button>
      </div>

      <div v-else-if="pedidos.length === 0" class="empty-state">
        <svg class="empty-icon" viewBox="0 0 24 24">
          <path d="M19,5h-2V3H7v2H5C3.9,5 3,5.9 3,7v1c0,2.55 1.92,4.63 4.39,4.94c0.63,1.5 1.98,2.63 3.61,2.96V19H7v2h10v-2h-4v-3.1c1.63-.33 2.98-1.46 3.61-2.96C19.08,12.63 21,10.55 21,8V7C21,5.9 20.1,5 19,5zM5,8V7h2v3.82C5.84,10.4 5,9.3 5,8zM19,8c0,1.3-0.84,2.4-2,2.82V7h2V8z"/>
        </svg>
        <h3>No hay pedidos registrados</h3>
        <p>Cuando realices pedidos, aparecerán aquí</p>
        <router-link to="/productos" class="shop-link">Explorar productos</router-link>
      </div>

      <transition-group v-else name="list" tag="div" class="pedidos-grid">
        <div 
          v-for="pedido in pedidos" 
          :key="pedido.id" 
          class="pedido-card"
          :class="{ 'expanded': pedido.id === pedidoAbierto }"
        >
          <div class="card-header" @click="togglePedido(pedido.id)">
            <div class="pedido-info">
              <span class="pedido-id">Pedido #{{ pedido.id }}</span>
              <span class="pedido-fecha">{{ formatFecha(pedido.created_at) }}</span>
            </div>
            <svg class="expand-icon" viewBox="0 0 24 24">
              <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/>
            </svg>
          </div>

          <transition name="slide">
            <div v-if="pedido.id === pedidoAbierto" class="card-details">
              <div class="subpedidos-container">
                <div 
                  v-for="subpedido in pedido.subpedidos" 
                  :key="subpedido.id" 
                  class="subpedido"
                >
                  <div class="distributor">
                    <svg class="distributor-icon" viewBox="0 0 24 24">
                      <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                    </svg>
                    <span>{{ subpedido.distribuidor.nombre_empresa }}</span>
                    <span class="subpedido-status" :class="estadoClass(subpedido.estado)">
                      {{ subpedido.estado }}
                    </span>
                  </div>

                  <div class="productos-list">
                    <div 
                      v-for="detalle in subpedido.detalles" 
                      :key="detalle.id" 
                      class="producto"
                    >
                      <img 
                        v-if="detalle.producto.imagen_url" 
                        :src="detalle.producto.imagen_url" 
                        alt="Imagen del producto"
                        class="producto-img"
                      />
                      <div v-else class="producto-img-placeholder"></div>
                      <div class="producto-info">
                        <span class="producto-nombre">{{ detalle.producto.nombre }}</span>
                        <span class="producto-cantidad">×{{ detalle.cantidad }}</span>
                        <span class="producto-precio">{{ formatearPrecio(detalle.producto.precio) }}</span>
                      </div>
                    </div>
                  </div>

                  <a
                    v-if="estadoPuedeFacturarse(subpedido.estado)"
                    :href="`http://localhost:8000/api/factura/${subpedido.id}`"
                    target="_blank"
                    class="factura-btn"
                  >
                    <svg class="download-icon" viewBox="0 0 24 24">
                      <path d="M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z"/>
                    </svg>
                    Descargar factura
                  </a>
                </div>
              </div>

              <div class="pedido-total">
                <span>Total del pedido:</span>
                <span class="total">{{ formatearPrecio(calcularTotalPedido(pedido)) }}</span>
              </div>
            </div>
          </transition>
        </div>
      </transition-group>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const pedidos = ref([])
const loading = ref(false)
const error = ref(null)
const pedidoAbierto = ref(null)

const cargarHistorial = async () => {
  loading.value = true
  error.value = null
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('/api/pedidos/tienda', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    pedidos.value = response.data
  } catch (err) {
    error.value = 'Error al cargar el historial de pedidos'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const togglePedido = (id) => {
  pedidoAbierto.value = pedidoAbierto.value === id ? null : id
}

const calcularTotalPedido = (pedido) => {
  return pedido.subpedidos.reduce((total, sub) => {
    return total + sub.detalles.reduce((subTotal, item) => {
      return subTotal + item.producto.precio * item.cantidad
    }, 0)
  }, 0)
}

const estadoClass = (estado) => {
  switch ((estado || '').toLowerCase()) {
    case 'pendiente': return 'status-pending'
    case 'procesado': return 'status-processing'
    case 'enviado':
    case 'en_camino': return 'status-shipped'
    case 'entregado': return 'status-delivered'
    case 'cancelado': return 'status-cancelled'
    default: return 'status-unknown'
  }
}

const estadoPuedeFacturarse = (estado) => {
  const facturables = ['aceptado', 'procesado', 'en_camino', 'entregado']
  return facturables.includes((estado || '').toLowerCase())
}

const formatearPrecio = (valor) => {
  if (valor == null) return ''
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(valor)
}

const formatFecha = (fechaString) => {
  if (!fechaString) return ''
  const fecha = new Date(fechaString)
  return fecha.toLocaleDateString('es-CO', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

onMounted(() => {
  cargarHistorial()
})
</script>

<style scoped>
/* Variables */
:root {
  --color-primary: #99D7A9;
  --color-primary-dark: #7dbe8d;
  --color-text: #2D3748;
  --color-text-light: #718096;
  --color-border: #E2E8F0;
  --color-bg: #FFFFFF;
  --color-error: #F56565;
  --color-success: #48BB78;
  --color-warning: #ED8936;
  --color-info: #4299E1;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.producto-img {
  width: 64px;
  height: 64px;
  object-fit: cover;
  border-radius: 0.5rem;
  border: 1px solid #ddd;
}


/* Estilos base */
.historial-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
  font-family: 'Inter', -apple-system, sans-serif;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.title {
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--color-text);
  margin: 0;
}

.refresh-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: var(--color-primary);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
}

.refresh-btn:hover {
  background: var(--color-primary-dark);
  transform: rotate(90deg);
}

.refresh-btn.loading {
  animation: spin 1s linear infinite;
}

.refresh-icon {
  width: 20px;
  height: 20px;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Estados */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid rgba(153, 215, 169, 0.2);
  border-radius: 50%;
  border-top-color: var(--color-primary);
  animation: spin 1s ease-in-out infinite;
  margin-bottom: 1rem;
}

.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
  text-align: center;
  color: var(--color-error);
}

.error-icon {
  width: 48px;
  height: 48px;
  fill: var(--color-error);
  margin-bottom: 1rem;
}

.retry-btn {
  margin-top: 1.5rem;
  padding: 0.75rem 1.5rem;
  background: var(--color-error);
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
}

.retry-btn:hover {
  background: #E53E3E;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
  text-align: center;
}

.empty-icon {
  width: 64px;
  height: 64px;
  fill: var(--color-text-light);
  opacity: 0.5;
  margin-bottom: 1.5rem;
}

.empty-state h3 {
  font-size: 1.25rem;
  color: var(--color-text);
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: var(--color-text-light);
  margin-bottom: 1.5rem;
}

.shop-link {
  padding: 0.75rem 1.5rem;
  background: var(--color-primary);
  color: white;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  transition: var(--transition);
}

.shop-link:hover {
  background: var(--color-primary-dark);
}

/* Lista de pedidos */
.pedidos-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

.pedido-card {
  background: var(--color-bg);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
}

.pedido-card.expanded {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem;
  cursor: pointer;
  transition: var(--transition);
}

.pedido-card.expanded .card-header {
  border-bottom: 1px solid var(--color-border);
}

.pedido-info {
  display: flex;
  flex-direction: column;
}

.pedido-id {
  font-weight: 600;
  color: var(--color-text);
}

.pedido-fecha {
  font-size: 0.875rem;
  color: var(--color-text-light);
}

.pedido-status {
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-pending {
  background: rgba(237, 137, 54, 0.1);
  color: var(--color-warning);
}

.status-processing {
  background: rgba(66, 153, 225, 0.1);
  color: var(--color-info);
}

.status-shipped {
  background: rgba(153, 215, 169, 0.1);
  color: var(--color-primary-dark);
}

.status-delivered {
  background: rgba(72, 187, 120, 0.1);
  color: var(--color-success);
}

.status-cancelled {
  background: rgba(245, 101, 101, 0.1);
  color: var(--color-error);
}

.status-unknown {
  background: rgba(113, 128, 150, 0.1);
  color: var(--color-text-light);
}

.expand-icon {
  width: 20px;
  height: 20px;
  fill: var(--color-text-light);
  transition: var(--transition);
}

.pedido-card.expanded .expand-icon {
  transform: rotate(180deg);
}

/* Detalles del pedido */
.card-details {
  padding: 1.25rem;
}

.slide-enter-active {
  animation: slideDown 0.3s ease-out;
}

.slide-leave-active {
  animation: slideDown 0.3s ease-out reverse;
}

@keyframes slideDown {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.subpedidos-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.subpedido {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.distributor {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  color: var(--color-text);
}

.distributor-icon {
  width: 18px;
  height: 18px;
  fill: var(--color-text-light);
}

.subpedido-status {
  margin-left: auto;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.6875rem;
  font-weight: 600;
}

.productos-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.producto {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  border-radius: 8px;
  background: rgba(153, 215, 169, 0.03);
}

.producto-img-placeholder {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  background: rgba(153, 215, 169, 0.1);
  flex-shrink: 0;
}

.producto-info {
  flex: 1;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.producto-nombre {
  flex: 1 1 100%;
  font-size: 0.875rem;
  color: var(--color-text);
}

.producto-cantidad {
  font-size: 0.8125rem;
  color: var(--color-text-light);
}

.producto-precio {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-text);
}

.factura-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1rem;
  background: var(--color-primary);
  color: black;
  border-radius: 6px;
  font-size: 0.8125rem;
  font-weight: 500;
  text-decoration: none;
  transition: var(--transition);
  margin-top: 0.5rem;
}

.factura-btn:hover {
  background: var(--color-primary-dark);
}

.download-icon {
  width: 16px;
  height: 16px;
  fill: black;
}

.pedido-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid var(--color-border);
  font-weight: 500;
  color: var(--color-text);
}

.total {
  font-weight: 600;
  color: var(--color-primary-dark);
}

/* Transiciones */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.list-move {
  transition: transform 0.5s ease;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Responsive */
@media (max-width: 768px) {
  .header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .pedido-card {
    border-radius: 8px;
  }
  
  .card-header {
    padding: 1rem;
  }
  
  .card-details {
    padding: 1rem;
  }
}
</style>