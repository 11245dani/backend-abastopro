<!-- HistorialPedidos.vue -->
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
            <strong>Estado general:</strong>
            <span class="estado-label estado-pendiente">
              {{ pedido.estado || '—' }}
            </span>
          </p>
          <p><strong>Fecha:</strong> {{ new Date(pedido.created_at).toLocaleString() }}</p>
        </div>

        <!-- Panel desplegable -->
        <transition name="desplegar">
          <div v-show="pedido.id === pedidoAbierto" class="panel-detalles">
            <h4>Subpedidos:</h4>

            <div v-for="subpedido in pedido.subpedidos" :key="subpedido.id" class="subpedido">
              <p><strong>Distribuidor:</strong> {{ subpedido.distribuidor.nombre_empresa }}</p>
              <p><strong>Estado:</strong> 
                <span :class="['estado-label', estadoClass(subpedido.estado)]">
                  {{ subpedido.estado }}
                </span>
              </p>

              <ul class="detalles-lista">
                                    <li 
                      v-for="detalle in subpedido.detalles" 
                      :key="detalle.id" 
                      class="detalle-item"
                    >
                      {{ detalle.producto.nombre }} × {{ detalle.cantidad }} – {{ formatearPrecio(detalle.producto.precio) }}
                    </li>
              </ul>

                      <!-- Botón para descargar factura si el subpedido ya fue aceptado o más -->
        <a
          v-if="estadoPuedeFacturarse(subpedido.estado)"
          :href="`http://localhost:8000/api/factura/${subpedido.id}`"
          target="_blank"
          class="btn-descargar-factura"
        >
          Descargar factura
        </a>

            </div>
                    <p class="total-pedido">
                      <strong>Total:</strong> {{ formatearPrecio(calcularTotalPedido(pedido)) }}
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
    const response = await axios.get('/api/pedidos/tienda', {
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

const calcularTotalPedido = (pedido) => {
  return pedido.subpedidos.reduce((total, sub) => {
    return total + sub.detalles.reduce((subTotal, item) => {
      return subTotal + item.producto.precio * item.cantidad
    }, 0)
  }, 0)
}

const estadoClass = (estado) => {
  switch ((estado || '').toLowerCase()) {
    case 'pendiente': return 'estado-pendiente'
    case 'procesado': return 'estado-procesado'
    case 'enviado': return 'estado-enviado'
    case 'entregado': return 'estado-entregado'
    case 'cancelado': return 'estado-cancelado'
    default: return 'estado-desconocido'
  }
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

// 👉 Para mostrar en input con puntos como 5.000
const formatearInputPrecio = (valor) => {
  if (valor == null || isNaN(valor)) return ''
  return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0 }).format(valor)
}

const estadoPuedeFacturarse = (estado) => {
  const facturables = ['aceptado', 'procesado', 'en_camino', 'entregado'];
  return facturables.includes((estado || '').toLowerCase());
};


onMounted(() => {
  cargarHistorial()
})
</script>

<style scoped>
.btn-descargar-factura {
  display: inline-block;
  margin-top: 10px;
  padding: 6px 12px;
  background-color: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 4px;
}
.btn-descargar-factura:hover {
  background-color: #0056b3;
}

</style>