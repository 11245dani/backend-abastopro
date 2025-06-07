
<template>
  <div class="pedidos-container">
    <h2>Pedidos Recibidos</h2>

    <div v-if="loading" class="loading">Cargando pedidos...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="pedidos.length === 0" class="no-pedidos">No hay pedidos disponibles.</div>

    <ul v-else class="pedidos-list">
      <li v-for="subpedido in pedidos" :key="subpedido.id" class="pedido-item">
        <div v-if="subpedido.pedido">
          <div class="pedido-header">
            <span class="pedido-id">Pedido ID: {{ subpedido.pedido.id }}</span>
            <span class="pedido-estado">Estado actual: {{ capitalizarEstado(subpedido.estado) }}</span>
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
              <label v-if="transicionesEstado[subpedido.estado]" for="estado-select">Cambiar estado:</label>
              <select
                v-if="transicionesEstado[subpedido.estado]"
                @change="cambiarEstado(subpedido.id, $event.target.value)"
              >
                <option disabled selected value="">Seleccione estado</option>
                <option
                  v-for="estado in transicionesEstado[subpedido.estado]"
                  :key="estado"
                  :value="estado"
                >
                  {{ capitalizarEstado(estado) }}
                </option>
              </select>
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

// Transiciones válidas según estado actual (coinciden con los valores del ENUM)
const transicionesEstado = {
  pendiente: ['aceptado', 'rechazado'],
  aceptado: ['en_camino'],
  en_camino: ['entregado']
};

const getImagenUrl = (imagenPath) => {
  if (!imagenPath) return '';
  return imagenPath.startsWith('http') ? imagenPath : `http://localhost:8000/storage/${imagenPath}`;
};

const formatearPrecio = (valor) => {
  if (valor == null || isNaN(valor)) return ''
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(valor)
};

const capitalizarEstado = (estado) => {
  return estado.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
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
  if (!nuevoEstado) return;
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
    alert('No se pudo cambiar el estado del subpedido: ' + (err.response?.data?.error || err.message));
  }
};

onMounted(() => {
  fetchPedidos();
});
</script>

<style scoped>
.pedidos-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Segoe UI', sans-serif;
}

.loading,
.error,
.no-pedidos {
  text-align: center;
  margin: 20px 0;
  font-weight: bold;
}

.pedido-item {
  border: 1px solid #ccc;
  border-radius: 12px;
  margin-bottom: 20px;
  padding: 15px;
  background-color: #fdfdfd;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.3s ease;
}

.pedido-item:hover {
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.pedido-header {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  margin-bottom: 10px;
  color: #333;
}

.pedido-estado {
  font-size: 14px;
  color: #888;
}

.productos-list {
  list-style: none;
  padding-left: 0;
  margin-top: 10px;
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

.producto-detalles {
  font-size: 15px;
  color: #444;
}

.acciones-pedido {
  margin-top: 15px;
}

.acciones-pedido button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  margin-right: 10px;
}

.acciones-pedido button:hover {
  background-color: #2980b9;
  transform: translateY(-1px);
}

.loading,
.no-pedidos {
  text-align: center;
  color: #555;
  font-size: 16px;
  margin: 20px 0;
}

.error {
  text-align: center;
  color: #e74c3c;
  font-weight: 500;
  font-size: 16px;
  margin: 20px 0;
}

/* Estilos adicionales por estado */
.btn-aprobar {
  background-color: #2ecc71;
}

.btn-aprobar:hover {
  background-color: #27ae60;
}

.btn-camino {
  background-color: #f39c12;
}

.btn-camino:hover {
  background-color: #e67e22;
}

.btn-entregado {
  background-color: #8e44ad;
}

.btn-entregado:hover {
  background-color: #71368a;
}
</style>
