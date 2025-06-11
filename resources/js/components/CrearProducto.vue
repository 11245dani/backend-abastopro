<template>
  <div class="crear-producto">
    <h2>Registrar nuevo producto</h2>

<!-- Sección para crear nueva categoría (solo si es admin) -->
<div v-if="rol === 'admin'" class="form-section">
  <label for="nuevaCategoria">Nueva categoría</label>
  <div class="input-group">
    <input v-model="nuevaCategoria" placeholder="Ej: Bebidas" />
    <button @click="crearCategoria" class="btn-secondary">Crear</button>
  </div>
</div>

<!-- Sección para crear nueva marca (solo si es admin) -->
<div v-if="rol === 'admin'" class="form-section">
  <label for="nuevaMarca">Nueva marca</label>
  <div class="input-group">
    <input v-model="nuevaMarca" placeholder="Ej: Coca-Cola" />
    <button @click="crearMarca" class="btn-secondary">Crear</button>
  </div>
</div>


    <!-- Formulario principal -->
    <form @submit.prevent="crearProducto" class="form-main">
      <div class="form-group">
        <label>Nombre</label>
        <input v-model="producto.nombre" required />
      </div>

      <div class="form-group">
        <label>Descripción</label>
        <textarea v-model="producto.descripcion" rows="3"></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Precio</label>
          <input
           type="text"
           v-model="precioFormateado"
            @input="formatearPrecio"
           placeholder="Ej: 2.000"
            required
          />
        </div>
        <div class="form-group">
          <label>Stock</label>
          <input type="number" v-model.number="producto.stock" min="0" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Categoría</label>
          <select v-model="producto.categoria_id" required>
            <option disabled value="">Seleccione una categoría</option>
            <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>Marca</label>
          <select v-model="producto.marca_id" required>
            <option disabled value="">Seleccione una marca</option>
            <option v-for="marca in marcas" :key="marca.id" :value="marca.id">{{ marca.nombre }}</option>
          </select>
        </div>
      </div>

<div class="form-group">
  <label>Imagen del producto</label>
  <input type="file" @change="handleImagenSeleccionada" accept="image/*" />
  <div v-if="previewUrl" class="preview">
    <img :src="previewUrl" alt="Vista previa" style="max-width: 200px; max-height: 200px;" />
  </div>
</div>
      <button type="submit" class="btn-primary">Guardar producto</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const rol = localStorage.getItem('rol') || ''


const categorias = ref([])
const marcas = ref([])
const nuevaCategoria = ref('')
const nuevaMarca = ref('')
const producto = ref({
  nombre: '',
  descripcion: '',
  precio: 0,
  stock: null,
  categoria_id: '',
  marca_id: '',
  imagen_url: ''
})

// Obtener datos al iniciar
const obtenerCategorias = async () => {
  const res = await axios.get('/api/categorias')
  categorias.value = res.data
}
const obtenerMarcas = async () => {
  const res = await axios.get('/api/marcas')
  marcas.value = res.data
}

onMounted(() => {
  obtenerCategorias()
  obtenerMarcas()
})

// Crear nueva categoría
const crearCategoria = async () => {
  if (!nuevaCategoria.value.trim()) return
  await axios.post('/api/categorias', { nombre: nuevaCategoria.value })
  nuevaCategoria.value = ''
  obtenerCategorias()
}

// Crear nueva marca
const crearMarca = async () => {
  if (!nuevaMarca.value.trim()) return
  await axios.post('/api/marcas', { nombre: nuevaMarca.value })
  nuevaMarca.value = ''
  obtenerMarcas()
}

const precioFormateado = ref('')

const formatearPrecio = () => {
  // Remover cualquier caracter que no sea dígito
  let soloNumeros = precioFormateado.value.replace(/\D/g, '')

  // Convertir a número para eliminar ceros a la izquierda
  let numero = parseInt(soloNumeros, 10)
  if (isNaN(numero)) {
    producto.value.precio = 0
    precioFormateado.value = ''
    return
  }

  producto.value.precio = numero

  // Formatear con separador de miles (puntos)
  precioFormateado.value = numero.toLocaleString('es-CO')
}

// Si quieres inicializar con producto.precio:
watch(() => producto.value.precio, (newVal) => {
  if (newVal !== null && newVal !== undefined) {
    precioFormateado.value = newVal.toLocaleString('es-CO')
  }
})

const imagenSeleccionada = ref(null)
const previewUrl = ref(null)

const handleImagenSeleccionada = (e) => {
  const file = e.target.files[0]
  if (file && file.type.startsWith('image/')) {
    imagenSeleccionada.value = file

    // Redimensionar usando canvas
    const reader = new FileReader()
    reader.onload = (event) => {
      const img = new Image()
      img.onload = () => {
        const canvas = document.createElement('canvas')
        const maxWidth = 800
        const scale = maxWidth / img.width
        canvas.width = maxWidth
        canvas.height = img.height * scale
        const ctx = canvas.getContext('2d')
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height)
        previewUrl.value = canvas.toDataURL('image/jpeg') // se puede usar también 'image/png'
      }
      img.src = event.target.result
    }
    reader.readAsDataURL(file)
  }
}


// Enviar formulario
const crearProducto = async () => {
  const formData = new FormData()
  formData.append('nombre', producto.value.nombre)
  formData.append('descripcion', producto.value.descripcion)
  formData.append('precio', producto.value.precio)
  formData.append('stock', producto.value.stock)
  formData.append('categoria_id', producto.value.categoria_id)
  formData.append('marca_id', producto.value.marca_id)
  if (imagenSeleccionada.value) {
    formData.append('imagen', imagenSeleccionada.value)
  }

  try {
    await axios.post('/api/productos', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data'
      }
    })
    alert('Producto creado con éxito')
  } catch (error) {
    alert('Error al crear producto')
    console.error(error)
  }
}

</script>

<style scoped>
.crear-producto {
  max-width: 700px;
  margin: 40px auto;
  background: #ffffff;
  border-radius: 16px;
  padding: 32px 42px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #222;
  transition: box-shadow 0.3s ease;
}

.crear-producto:hover {
  box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
}

h2 {
  font-weight: 700;
  font-size: 30px;
  margin-bottom: 30px;
  color: #111;
}

/* Secciones adicionales */
.form-section {
  margin-bottom: 30px;
}

label {
  display: block;
  margin-bottom: 10px;
  font-weight: 600;
  color: #444;
}

.input-group {
  display: flex;
  gap: 12px;
}

.input-group input {
  flex: 1;
  padding: 12px 16px;
  border-radius: 12px;
  border: 1.8px solid #ccc;
  font-size: 15px;
  background: #f9f9f9;
  transition: all 0.3s ease;
}
.input-group input:focus {
  border-color: #000;
  background: #fff;
  outline: none;
}

.btn-secondary {
  background: #444;
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 12px 18px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 5px 12px rgba(0, 0, 0, 0.08);
  transition: background-color 0.25s ease, transform 0.25s ease;
}
.btn-secondary:hover {
  background: #666;
  transform: translateY(-2px);
}

/* Formulario principal */
.form-main .form-group {
  margin-bottom: 24px;
}

.form-row {
  display: flex;
  gap: 20px;
  margin-bottom: 24px;
}

.form-row .form-group {
  flex: 1;
}

input, select, textarea {
  width: 100%;
  padding: 12px 16px;
  border-radius: 12px;
  border: 1.5px solid #ccc;
  font-size: 16px;
  background: #f9f9f9;
  color: #222;
  transition: border-color 0.3s ease, background-color 0.3s ease;
}

input:focus, select:focus, textarea:focus {
  border-color: #000;
  background: #fff;
  outline: none;
}

textarea {
  resize: vertical;
}

.btn-primary {
  background-color: #000;
  color: #fff;
  padding: 14px 0;
  width: 100%;
  font-weight: 700;
  font-size: 18px;
  border-radius: 14px;
  border: none;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.25s ease;
}

.btn-primary:hover {
  background-color: #222;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
  transform: translateY(-3px);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

/* Vista previa imagen */
.preview img {
  border-radius: 10px;
  margin-top: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>