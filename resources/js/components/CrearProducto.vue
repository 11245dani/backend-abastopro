<template>
  <div class="crear-producto">
    <h2>Registrar nuevo producto</h2>

    <!-- Sección para crear nueva categoría -->
    <div class="form-section">
      <label for="nuevaCategoria">Nueva categoría</label>
      <div class="input-group">
        <input v-model="nuevaCategoria" placeholder="Ej: Bebidas" />
        <button @click="crearCategoria" class="btn-secondary">Crear</button>
      </div>
    </div>

    <!-- Sección para crear nueva marca -->
    <div class="form-section">
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
          <input type="number" v-model.number="producto.precio" min="0" required />
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
        <label>Imagen URL (opcional)</label>
        <input v-model="producto.imagen_url" />
      </div>

      <button type="submit" class="btn-primary">Guardar producto</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const categorias = ref([])
const marcas = ref([])
const nuevaCategoria = ref('')
const nuevaMarca = ref('')
const producto = ref({
  nombre: '',
  descripcion: '',
  precio: null,
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

// Enviar formulario
const crearProducto = async () => {
  try {
    await axios.post('/api/productos', producto.value, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
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
  max-width: 650px;
  margin: 40px auto;
  background: #fff;
  border-radius: 14px;
  padding: 30px 40px;
  box-shadow: 0 12px 25px rgba(0,0,0,0.12);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #222;
  transition: box-shadow 0.3s ease;
}
.crear-producto:hover {
  box-shadow: 0 18px 40px rgba(0,0,0,0.18);
}

h2 {
  font-weight: 700;
  font-size: 28px;
  margin-bottom: 25px;
  color: #111;
  user-select: none;
}

/* Secciones para nueva categoría y marca */
.form-section {
  margin-bottom: 28px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #444;
  user-select: none;
}

.input-group {
  display: flex;
  gap: 12px;
}

.input-group input {
  flex: 1;
  padding: 12px 15px;
  border-radius: 10px;
  border: 1.8px solid #ccc;
  font-size: 15px;
  transition: border-color 0.3s ease;
  background: #fafafa;
  color: #222;
}
.input-group input:focus {
  border-color: #222;
  background: #fff;
  outline: none;
}

.btn-secondary {
  background: #222;
  color: #f5f5f5;
  border: none;
  border-radius: 10px;
  padding: 12px 18px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 5px 12px rgba(0,0,0,0.1);
  transition: background-color 0.25s ease, transform 0.25s ease;
}
.btn-secondary:hover {
  background: #555;
  transform: translateY(-2px);
}

/* Formulario principal */
.form-main .form-group {
  margin-bottom: 22px;
}

.form-main label {
  color: #555;
}

input, select, textarea {
  width: 100%;
  padding: 12px 16px;
  border-radius: 12px;
  border: 1.5px solid #ccc;
  font-size: 16px;
  color: #222;
  background: #fafafa;
  transition: border-color 0.3s ease, background-color 0.3s ease;
  resize: vertical;
}

input:focus, select:focus, textarea:focus {
  border-color: #000;
  background: #fff;
  outline: none;
}

.form-row {
  display: flex;
  gap: 20px;
}

.form-row .form-group {
  flex: 1;
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
  box-shadow: 0 8px 20px rgba(0,0,0,0.18);
  transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.25s ease;
  user-select: none;
}

.btn-primary:hover {
  background-color: #222;
  box-shadow: 0 12px 30px rgba(0,0,0,0.3);
  transform: translateY(-3px);
}

.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}
</style>
