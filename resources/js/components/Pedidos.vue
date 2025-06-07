<template>
  <div class="pedidos-container">
    <h2>Pedidos Recibidos</h2>

    <div v-if="loading" class="loading">Cargando pedidos...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="pedidos.length === 0" class="no-pedidos">No hay pedidos disponibles.</div>

    <ul v-else class="pedidos-list">
      <li
        v-for="subpedido in pedidos"
        :key="subpedido.id"
        class="pedido-item"
      >
        <div v-if="subpedido.pedido">
          <div class="pedido-header">
            <span class="pedido-id">Pedido ID: {{ subpedido.pedido.id }}</span>
            <span class="pedido-estado">Estado: {{ subpedido.estado }}</span>
          </div>

          <div class="pedido-body">
            <p><strong>Tienda:</strong> {{ subpedido.pedido.tienda?.usuario?.nombre || 'N/A' }}</p>

            <div v-if="subpedido.detalles.length > 0">
              <p><strong>Productos:</strong></p>
              <ul class="productos-list">
                <li v-for="detalle in subpedido.detalles" :key="detalle.id" class="producto-item">
                  <div class="producto-info">
                    <img
                      v-if="detalle.producto?.imagen"
                      :src="getImagenUrl(detalle.producto.imagen)"
                      alt="Imagen del producto"
                      class="producto-imagen"
                    />
                            <div class="producto-detalles">
                              {{ detalle.producto?.nombre }} (x{{ detalle.cantidad }}) - {{ formatearPrecio(detalle.precio_unitario) }}
                            </div>
                  </div>
                </li>
              </ul>
            </div>

            <div class="acciones-pedido">

              <button v-if="subpedido.estado === 'pendiente'" @click="cambiarEstado(subpedido.id, 'aceptado')">
                Aprobar
              </button>
              <button v-if="subpedido.estado === 'aceptado'" @click="cambiarEstado(subpedido.id, 'en_camino')">
                Marcar como En Camino
              </button>
              <button v-if="subpedido.estado === 'en_camino'" @click="cambiarEstado(subpedido.id, 'entregado')">
                Marcar como Entregado
              </button>
            </div>
          </div>
        </div>
        <div v-else>
          <p>Error: información del pedido no disponible.</p>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const pedidos = ref([]);
const loading = ref(false);
const error = ref(null);

// Construye la URL completa de la imagen si es relativa
const getImagenUrl = (imagenPath) => {
  if (!imagenPath) return '';
  return imagenPath.startsWith('http') ? imagenPath : `http://localhost:8000/storage/${imagenPath}`;
};

const fetchPedidos = async () => {
  loading.value = true;
  error.value = null;
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get('http://localhost:8000/api/pedidos/distribuidor', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    pedidos.value = response.data;
  } catch (err) {
    error.value = 'Error al cargar pedidos.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const cambiarEstado = async (subpedidoId, nuevoEstado) => {
  try {
    const token = localStorage.getItem('token');
    await axios.patch(`http://localhost:8000/api/subpedidos/${subpedidoId}/estado`, {
      estado: nuevoEstado
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    await fetchPedidos(); // Refresca los pedidos tras actualizar
  } catch (err) {
    console.error('Error al cambiar estado:', err.response?.data || err.message);
    alert('No se pudo cambiar el estado del subpedido.');
  }
};

const formatearPrecio = (valor) => {
  if (valor == null || isNaN(valor)) return ''
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(valor)
}


onMounted(() => {
  fetchPedidos();
});
</script>

<style scoped>
.pedidos-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.pedido-item {
  border: 1px solid #ccc;
  border-radius: 12px;
  margin-bottom: 20px;
  padding: 15px;
  background-color: #f9f9f9;
}

.pedido-header {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  margin-bottom: 10px;
}

.productos-list {
  list-style: none;
  padding-left: 0;
}

.producto-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.producto-imagen {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  margin-right: 10px;
  border: 1px solid #ddd;
}

.acciones-pedido {
  margin-top: 15px;
}

.acciones-pedido button {
  margin-right: 10px;
}
</style>
