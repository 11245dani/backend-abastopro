<template>
  <div class="historial-pedidos">
    <h2>Mis Pedidos</h2>

    <div v-if="loading" class="estado-carga">Cargando historial...</div>
    <div v-if="error" class="estado-error">{{ error }}</div>

    <ul v-if="pedidos.length > 0" class="lista-pedidos">
      <li 
        v-for="pedido in pedidos" 
        :key="pedido.id" 
        class="pedido-item"
        :class="{ abierto: pedido.id === pedidoAbierto }"
        @click="togglePedido(pedido.id)"
        tabindex="0"
        @keydown.enter.prevent="togglePedido(pedido.id)"
        role="button"
        aria-expanded="pedido.id === pedidoAbierto"
      >
        <div class="resumen-pedido">
          <p><strong>ID:</strong> {{ pedido.id }}</p>
          <p>
            <strong>Estado:</strong>
            <span :class="['estado-label', pedido.estado ? estadoClass(pedido.estado) : 'estado-desconocido']">
              {{ pedido.estado }}
            </span>
          </p>
          <p><strong>Fecha:</strong> {{ new Date(pedido.created_at).toLocaleString() }}</p>
        </div>

        <!-- Panel desplegable -->
        <transition name="desplegar">
          <div v-show="pedido.id === pedidoAbierto" class="panel-detalles">
            <h4>Detalles del pedido:</h4>
            <ul class="detalles-lista">
              <li v-for="item in pedido.detalles" :key="item.id" class="detalle-item">
                {{ item.producto.nombre }} × {{ item.cantidad }} – ${{ item.producto.precio }}
              </li>
            </ul>
            <p class="total-pedido">
              <strong>Total:</strong> $
              {{
                pedido.detalles.reduce((acc, i) => acc + i.producto.precio * i.cantidad, 0).toFixed(2)
              }}
            </p>
          </div>
        </transition>

        <hr />
      </li>
    </ul>

    <div v-else class="sin-pedidos">No tienes pedidos registrados.</div>
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
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('/api/historial-pedidos', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    pedidos.value = response.data
  } catch (err) {
    error.value = 'Error al cargar el historial de pedidos.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const togglePedido = (id) => {
  pedidoAbierto.value = pedidoAbierto.value === id ? null : id
}

// Función para asignar clase CSS basada en estado para estilizar con negro/blanco
const estadoClass = (estado) => {
  switch (estado.toLowerCase()) {
    case 'pendiente':
      return 'estado-pendiente'
    case 'procesado':
      return 'estado-procesado'
    case 'enviado':
      return 'estado-enviado'
    case 'entregado':
      return 'estado-entregado'
    case 'cancelado':
      return 'estado-cancelado'
    default:
      return 'estado-desconocido'
  }
}

onMounted(() => {
  cargarHistorial()
})
</script>

<style scoped>
.historial-pedidos {
  max-width: 700px;
  margin: 2rem auto;
  padding: 1.5rem 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #121212;
  color: #f0f0f0;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.7);
  user-select: none;
  transition: background-color 0.4s ease, color 0.4s ease;
}

h2 {
  text-align: center;
  font-weight: 700;
  margin-bottom: 1.5rem;
  letter-spacing: 1.2px;
  color: #e0e0e0;
}

.estado-carga,
.estado-error,
.sin-pedidos {
  text-align: center;
  font-weight: 600;
  margin-top: 3rem;
  color: #bbb;
  font-size: 1.2rem;
  font-style: italic;
}

.lista-pedidos {
  list-style: none;
  padding: 0;
  margin: 0;
}

.pedido-item {
  padding: 1rem 1.2rem;
  margin-bottom: 1.3rem;
  border-radius: 10px;
  background: #1e1e1e;
  box-shadow: 0 2px 10px rgba(255 255 255 / 0.1);
  transition:
    background-color 0.35s ease,
    box-shadow 0.35s ease,
    transform 0.3s ease;
  cursor: pointer;
  outline: none;
  position: relative;
}

.pedido-item:hover,
.pedido-item:focus-visible {
  background-color: #2a2a2a;
  box-shadow: 0 6px 18px rgba(255 255 255 / 0.15);
  transform: translateY(-4px);
}

.pedido-item.abierto {
  background-color: #333;
  box-shadow: 0 8px 24px rgba(255 255 255 / 0.3);
  transform: translateY(-6px);
}

.resumen-pedido p {
  margin: 0.2rem 0;
  user-select: none;
}

.detalles-lista {
  margin-top: 0.8rem;
  padding-left: 1.2rem;
  color: #ddd;
  font-size: 0.95rem;
}

.detalle-item {
  margin-bottom: 0.3rem;
  transition: color 0.3s ease;
}

.detalle-item:hover {
  color: #fff;
}

.total-pedido {
  margin-top: 0.8rem;
  font-weight: 700;
  color: #fafafa;
  border-top: 1px solid #444;
  padding-top: 0.5rem;
}

/* Etiquetas de estado con paleta blanco/negro y estilos elegantes */
.estado-label {
  display: inline-block;
  padding: 0.25rem 0.7rem;
  border-radius: 14px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: capitalize;
  letter-spacing: 0.05em;
  user-select: none;
  transition: background-color 0.4s ease, color 0.4s ease;
}

/* Estados personalizados en blanco y negro */
.estado-pendiente {
  background-color: #222;
  color: #fff;
  border: 1.8px solid #bbb;
}

.estado-procesado {
  background-color: #fff;
  color: #000;
  border: 1.8px solid #444;
}

.estado-enviado {
  background-color: #111;
  color: #eee;
  border: 1.8px solid #888;
}

.estado-entregado {
  background-color: #fff;
  color: #111;
  border: 1.8px solid #222;
}

.estado-cancelado {
  background-color: #000;
  color: #f44336; /* rojo suave para cancelar */
  border: 1.8px solid #f44336;
}

.estado-desconocido {
  background-color: #555;
  color: #ddd;
  border: 1.8px solid #999;
}

/* Separador */
hr {
  border: none;
  border-top: 1px solid #444;
  margin: 1rem 0 0.5rem 0;
  opacity: 0.4;
  transition: opacity 0.4s ease;
}

hr:hover {
  opacity: 0.8;
}

/* Transición para panel desplegable */
.desplegar-enter-active, .desplegar-leave-active {
  transition: max-height 0.4s ease, opacity 0.4s ease, padding 0.4s ease;
}
.desplegar-enter-from, .desplegar-leave-to {
  max-height: 0;
  opacity: 0;
  padding-top: 0;
  padding-bottom: 0;
}
.desplegar-enter-to, .desplegar-leave-from {
  max-height: 500px; /* suficiente para expandir */
  opacity: 1;
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.panel-detalles {
  color: #ccc;
  font-size: 0.9rem;
  border-top: 1px solid #444;
  user-select: text;
}
</style>
