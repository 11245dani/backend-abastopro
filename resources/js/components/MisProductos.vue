<template>
  <div class="mis-productos">
    <div class="encabezado">
      <h2>Mis Productos</h2>
      <button class="btn-anadir" @click="añadirProducto">+ Añadir producto</button>
    </div>

    <div v-if="productos.length === 0">
      No tienes productos registrados.
    </div>

    <div class="productos-grid">
      <div v-for="prod in productos" :key="prod.id" class="producto-card">
        <img v-if="prod.imagen_url" :src="prod.imagen_url" class="producto-imagen" />

        <h3>{{ prod.nombre }}</h3>
        <p class="categoria">{{ prod.categoria?.nombre }}</p>

        <div class="precio-stock">
          <p class="precio">$ {{ prod.precio }}</p>
          <p class="stock">Stock: {{ prod.stock }}</p>
        </div>

        <div class="acciones">
          <button class="btn-editar" @click="prepararEdicion(prod)">✏️ Editar</button>
          <button class="btn-eliminar" @click="eliminarProducto(prod.id)">🗑️ Eliminar</button>
        </div>
      </div>
    </div>

<!-- Modal flotante -->
<div v-if="productoEditar" class="modal-overlay">
  <div class="modal">
    <h3>Editar Producto</h3>
    <form @submit.prevent="guardarEdicion">
      <label>Nombre</label>
      <input v-model="productoEditar.nombre" placeholder="Nombre" required />

      <label>Descripción</label>
      <textarea v-model="productoEditar.descripcion" placeholder="Descripción"></textarea>

      <label>Precio</label>
      <input type="number" v-model.number="productoEditar.precio" min="0" placeholder="Precio" required />

      <label>Stock</label>
      <input type="number" v-model.number="productoEditar.stock" min="0" placeholder="Stock" required />

      <label>URL de imagen</label>
      <input v-model="productoEditar.imagen_url" placeholder="URL de imagen" />

      <div class="modal-botones">
        <button type="submit" class="btn-guardar">Guardar</button>
        <button type="button" class="btn-cancelar" @click="productoEditar = null">Cancelar</button>
      </div>
    </form>
  </div>
</div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const productos = ref([])
const productoEditar = ref(null)

const obtenerProductos = async () => {
  try {
    const res = await axios.get('/api/productos', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    productos.value = res.data
  } catch (error) {
    alert('Error al cargar productos')
  }
}

const eliminarProducto = async (id) => {
  if (confirm('¿Estás seguro de eliminar este producto?')) {
    try {
      await axios.delete(`/api/productos/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      })
      productos.value = productos.value.filter(p => p.id !== id)
    } catch (error) {
      alert('Error al eliminar producto')
    }
  }
}

const prepararEdicion = (producto) => {
  productoEditar.value = { ...producto }
}

const guardarEdicion = async () => {
  try {
    const { id, ...data } = productoEditar.value
    await axios.put(`/api/productos/${id}`, data, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    await obtenerProductos()
    productoEditar.value = null
  } catch (error) {
    alert('Error al guardar cambios')
  }
}

const añadirProducto = () => {
  router.push('/CrearProducto')
}

onMounted(() => {
  obtenerProductos()
})
</script>

<style scoped>
.mis-productos {
  max-width: 1100px;
  margin: auto;
}

.encabezado {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.productos-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}

.producto-card {
  flex: 1 1 calc(25% - 20px);
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 15px;
  background: #f9f9f9;
  text-align: center;
  min-width: 200px;
  max-width: 250px;
}

.producto-imagen {
  width: 100px;
  height: 100px;
  object-fit: cover;
  margin-bottom: 10px;
  border-radius: 6px;
}

h3 {
  font-size: 1.2em;
  margin: 5px 0;
}

.categoria {
  font-size: 0.9em;
  color: #555;
  margin-bottom: 10px;
}

.precio-stock {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 10px;
  font-weight: bold;
}

.precio {
  color: #222;
}

.stock {
  color: #888;
}

.acciones {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.btn-editar {
  background-color: white;
  color: black;
  border: 1px solid #888;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}

.btn-editar:hover {
  background-color: #f0f0f0;
}

.btn-eliminar {
  background-color: red;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}

.btn-eliminar:hover {
  background-color: darkred;
}

.btn-anadir {
  background-color: black;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-anadir:hover {
  background-color: #333;
}

/* MODAL flotante */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  width: 400px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.modal form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.modal input,
.modal textarea {
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
}

.modal-botones {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.btn-guardar {
  background-color: #28a745;
  color: white;
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn-cancelar {
  background-color: #ccc;
  color: black;
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
</style>
