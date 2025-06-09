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
          <p class="precio">{{ formatearPrecio(prod.precio) }}</p>
          <p class="stock">Stock: {{ prod.stock }}</p>
        </div>
        <div class="acciones">
          <button class="btn-editar" @click="prepararEdicion(prod)">✏️ Editar</button>
          <button class="btn-eliminar" @click="eliminarProducto(prod.id)">🗑️ Eliminar</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="productoEditar" class="modal-overlay">
      <div class="modal">
        <h3>Editar Producto</h3>
        <form @submit.prevent="guardarEdicion" class="formulario-modal">
          <div class="campo">
            <label>Nombre</label>
            <input v-model="productoEditar.nombre" required />
          </div>

          <div class="campo">
            <label>Descripción</label>
            <textarea v-model="productoEditar.descripcion" rows="2"></textarea>
          </div>

          <div class="campo">
            <label>Precio</label>
            <input
              type="text"
              :value="formatearInputPrecio(productoEditar.precio)"
              @input="actualizarPrecio($event.target.value)"
              placeholder="Precio"
              required
            />
          </div>

          <div class="campo">
            <label>Stock</label>
            <input type="number" v-model.number="productoEditar.stock" min="0" required />
          </div>

          <div class="campo">
            <label>Imagen actual</label>
            <div v-if="productoEditar.imagen_url">
              <img :src="productoEditar.imagen_url" class="imagen-preview" />
              <button type="button" @click="productoEditar.eliminar_imagen = true; productoEditar.imagen_url = null">
                ❌ Eliminar imagen
              </button>
            </div>
          </div>

          <div class="campo">
            <label>Nueva imagen</label>
            <input type="file" @change="handleImagenSeleccionada" />
          </div>

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
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const productos = ref([])
const productoEditar = ref(null)
const imagenNueva = ref(null)

const obtenerProductos = async () => {
  try {
    const res = await axios.get('/api/productos', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
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
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
      })
      productos.value = productos.value.filter(p => p.id !== id)
    } catch (error) {
      alert('Error al eliminar producto')
    }
  }
}

const prepararEdicion = (producto) => {
  productoEditar.value = { ...producto, eliminar_imagen: false }
}

const handleImagenSeleccionada = (e) => {
  imagenNueva.value = e.target.files[0]
}

const guardarEdicion = async () => {
  try {
    const formData = new FormData()
    formData.append('nombre', productoEditar.value.nombre)
    formData.append('descripcion', productoEditar.value.descripcion || '')
    formData.append('precio', productoEditar.value.precio)
    formData.append('stock', productoEditar.value.stock)
    formData.append('categoria_id', productoEditar.value.categoria_id)
    formData.append('marca_id', productoEditar.value.marca_id)

    if (imagenNueva.value) {
      formData.append('imagen', imagenNueva.value)
    }

    if (productoEditar.value.eliminar_imagen) {
      formData.append('eliminar_imagen', '1')
    }

    await axios.post(`/api/productos/${productoEditar.value.id}?_method=PUT`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data',
      }
    })

    await obtenerProductos()
    productoEditar.value = null
    imagenNueva.value = null
  } catch (error) {
    alert('Error al guardar cambios')
  }
}

const añadirProducto = () => {
  router.push('/CrearProducto')
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

// 👉 Para actualizar el precio interno al número limpio
const actualizarPrecio = (valorFormateado) => {
  const soloNumeros = valorFormateado.replace(/\./g, '').replace(/\D/g, '')
  productoEditar.value.precio = parseInt(soloNumeros) || 0
}

onMounted(obtenerProductos)
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
.imagen-preview {
  max-width: 80px;
  max-height: 80px;
  object-fit: cover;
  border-radius: 6px;
  margin-bottom: 10px;
  border: 1px solid #ddd;
}


.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.4); /* fondo oscuro translúcido */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000; /* por encima del contenido */
}


/* Mejora visual del modal */
.modal {
  background: linear-gradient(to bottom right, #ffffff, #f7f7f7);
  border-radius: 16px;
  padding: 30px;
  width: 420px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  animation: slideIn 0.3s ease-out;
}

.modal h3 {
  font-size: 1.4rem;
  margin-bottom: 15px;
  color: #333;
}

.modal input,
.modal textarea {
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  background-color: #fafafa;
  transition: border-color 0.2s;
}

.modal input:focus,
.modal textarea:focus {
  outline: none;
  border-color: #4caf50;
}

.modal label {
  font-weight: 600;
  font-size: 13px;
  margin-top: 8px;
  color: #555;
}

.modal-botones {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 10px;
}

.btn-guardar {
  background-color: #4caf50;
  color: white;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-guardar:hover {
  background-color: #43a047;
}

.btn-cancelar {
  background-color: #e0e0e0;
  color: #333;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-cancelar:hover {
  background-color: #bdbdbd;
}

.formulario-modal .campo {
  margin-bottom: 14px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.formulario-modal input[type="number"] {
  -moz-appearance: textfield;
}

.formulario-modal input::-webkit-outer-spin-button,
.formulario-modal input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
