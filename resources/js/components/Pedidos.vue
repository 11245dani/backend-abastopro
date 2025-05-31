<template>
  <div class="pedidos-container">
    <h2>Pedidos Recibidos</h2>

    <div v-if="loading" class="loading">Cargando pedidos...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="pedidos.length === 0" class="no-pedidos">No hay pedidos disponibles.</div>

    <ul v-else class="pedidos-list">
      <li
        v-for="pedido in pedidos"
        :key="pedido.id"
        class="pedido-item"
      >
        <div v-if="pedido.pedido">
          <div class="pedido-header">
            <span class="pedido-id">Pedido ID: {{ pedido.pedido.id }}</span>
            <span class="pedido-cantidad">Cantidad: {{ pedido.cantidad }}</span>
          </div>
          <div class="pedido-body">
            <p><strong>Tienda:</strong> {{ pedido.pedido.tienda?.usuario?.nombre || 'N/A' }}</p>
            <p><strong>Producto:</strong> {{ pedido.producto?.nombre || 'N/A' }}</p>
            <p><strong>Estado:</strong> {{ pedido.pedido.estado }}</p>

            <!-- Botones condicionales según el estado -->
            <div class="acciones-pedido">
              <button v-if="pedido.pedido.estado === 'pendiente'" @click="cambiarEstado(pedido.pedido.id, 'procesado')">
                Aprobar
              </button>
              <button v-if="pedido.pedido.estado === 'aceptado'" @click="cambiarEstado(pedido.pedido.id, 'en_camino')">
                Marcar como En Camino
              </button>
              <button v-if="pedido.pedido.estado === 'en_camino'" @click="cambiarEstado(pedido.pedido.id, 'entregado')">
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

const fetchPedidos = async () => {
  loading.value = true;
  error.value = null;
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get('http://localhost:8000/api/pedidos-distribuidor', {
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

const cambiarEstado = async (pedidoId, nuevoEstado) => {
  try {
    const token = localStorage.getItem('token');
    await axios.patch(`http://localhost:8000/api/pedidos/${pedidoId}/estado`, {
      estado: nuevoEstado
    }, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    await fetchPedidos(); // Recargar la lista tras el cambio
  } catch (err) {
    console.error('Error al cambiar estado:', err.response?.data || err.message);
    alert('No se pudo cambiar el estado del pedido.');
  }
};

onMounted(() => {
  fetchPedidos();
});
</script>

<style scoped>
.pedidos-container {
  max-width: 700px;
  margin: 30px auto;
  padding: 25px 30px;
  background-color: #111;
  color: #eee;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.pedidos-container h2 {
  margin-bottom: 25px;
  font-weight: 700;
  font-size: 28px;
  border-bottom: 2px solid #444;
  padding-bottom: 10px;
  color: #f0f0f0;
}

.loading,
.error,
.no-pedidos {
  text-align: center;
  font-size: 16px;
  padding: 15px 0;
}

.error {
  color: #ff4c4c;
}

.pedidos-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.pedido-item {
  background-color: #222;
  margin-bottom: 15px;
  padding: 20px 25px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  cursor: default;
}

.pedido-item:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.9);
}

.pedido-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-weight: 600;
  color: #ddd;
  font-size: 16px;
}

.pedido-id {
  color: #ccc;
}

.pedido-cantidad {
  background-color: #444;
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 14px;
  color: #eee;
  user-select: none;
}

.pedido-body p {
  margin: 6px 0;
  color: #bbb;
  font-size: 15px;
  line-height: 1.4;
}

.pedido-body strong {
  color: #fff;
}

.acciones-pedido button {
  margin-top: 10px;
  margin-right: 8px;
  padding: 6px 14px;
  font-size: 14px;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  background-color: #3a8;
  color: white;
  cursor: pointer;
  transition: background-color 0.2s;
}

.acciones-pedido button:hover {
  background-color: #2a6;
}
</style>
