<template>
  <div class="crear-producto">
    <!-- Header con navegación -->
    <div class="header-navegacion">
      <button class="btn-volver" @click="volverAtras">
        <span class="icono-volver">←</span>
        <span>Volver</span>
      </button>
      <div class="titulo-seccion">
        <h2>✨ Registrar nuevo producto</h2>
        <p class="subtitulo">Completa la información de tu producto</p>
      </div>
    </div>

    <div class="contenedor-formulario">
      <!-- Secciones para admin -->
      <div v-if="rol === 'admin'" class="admin-sections">
        <div class="admin-card">
          <h3>🏷️ Gestión de Categorías</h3>
          <div class="form-section">
            <label for="nuevaCategoria">Nueva categoría</label>
            <div class="input-group">
              <input 
                v-model="nuevaCategoria" 
                placeholder="Ej: Bebidas, Snacks, Electrónicos..." 
                @keyup.enter="crearCategoria"
              />
              <button @click="crearCategoria" class="btn-crear" :disabled="!nuevaCategoria.trim()">
                <span class="btn-icono">+</span>
                <span>Crear</span>
              </button>
            </div>
          </div>
          
          <div class="form-section">
            <h3>🔖 Gestión de Marcas</h3>
            <label for="nuevaMarca">Nueva marca</label>
            <div class="input-group">
              <input 
                v-model="nuevaMarca" 
                placeholder="Ej: Coca-Cola, Nike, Samsung..." 
                @keyup.enter="crearMarca"
              />
              <button @click="crearMarca" class="btn-crear" :disabled="!nuevaMarca.trim()">
                <span class="btn-icono">+</span>
                <span>Crear</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulario principal -->
      <form @submit.prevent="crearProducto" class="form-main">
        <div class="form-card">
          <h3>📋 Información Básica</h3>
          
          <div class="form-group">
            <label>🏷️ Nombre del producto</label>
            <input 
              v-model="producto.nombre" 
              placeholder="Ej: iPhone 15 Pro Max 256GB..."
              required 
            />
          </div>

          <div class="form-group">
            <label>📝 Descripción</label>
            <textarea 
              v-model="producto.descripcion" 
              rows="3"
              placeholder="Describe las características principales de tu producto..."
            ></textarea>
          </div>
        </div>

        <div class="form-card">
          <h3>💰 Precio y Stock</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label>💵 Precio</label>
              <div class="precio-input">
                <span class="moneda">$</span>
                <input
                  type="text"
                  v-model="precioFormateado"
                  @input="formatearPrecio"
                  placeholder="2.000"
                  required
                />
              </div>
            </div>
            <div class="form-group">
              <label>📊 Stock inicial</label>
              <input 
                type="number" 
                v-model.number="producto.stock" 
                min="0" 
                placeholder="Cantidad disponible"
                required 
              />
            </div>
          </div>
        </div>

        <div class="form-card">
          <h3>📂 Categorización</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label>🏷️ Categoría</label>
              <div class="select-container">
                <div class="select-wrapper" @click="toggleCategoriaDropdown">
                  <input 
                    type="text" 
                    v-model="busquedaCategoria"
                    :placeholder="categoriaSeleccionada || 'Buscar o seleccionar categoría...'"
                    @input="filtrarCategorias"
                    @focus="mostrarDropdownCategoria = true"
                    class="select-input"
                  />
                  <span class="dropdown-arrow" :class="{ 'rotated': mostrarDropdownCategoria }">▼</span>
                </div>
                
                <div v-if="mostrarDropdownCategoria" class="dropdown-list">
                  <div v-if="categoriasFiltradas.length === 0" class="dropdown-empty">
                    No se encontraron categorías
                  </div>
                  <div 
                    v-for="cat in categoriasFiltradas" 
                    :key="cat.id" 
                    class="dropdown-item"
                    @click="seleccionarCategoria(cat)"
                  >
                    {{ cat.nombre }}
                  </div>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label>🔖 Marca</label>
              <div class="select-container">
                <div class="select-wrapper" @click="toggleMarcaDropdown">
                  <input 
                    type="text" 
                    v-model="busquedaMarca"
                    :placeholder="marcaSeleccionada || 'Buscar o seleccionar marca...'"
                    @input="filtrarMarcas"
                    @focus="mostrarDropdownMarca = true"
                    class="select-input"
                  />
                  <span class="dropdown-arrow" :class="{ 'rotated': mostrarDropdownMarca }">▼</span>
                </div>
                
                <div v-if="mostrarDropdownMarca" class="dropdown-list">
                  <div v-if="marcasFiltradas.length === 0" class="dropdown-empty">
                    No se encontraron marcas
                  </div>
                  <div 
                    v-for="marca in marcasFiltradas" 
                    :key="marca.id" 
                    class="dropdown-item"
                    @click="seleccionarMarca(marca)"
                  >
                    {{ marca.nombre }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-card">
          <h3>📸 Imagen del producto</h3>
          
          <div class="imagen-upload">
            <input 
              type="file" 
              @change="handleImagenSeleccionada" 
              accept="image/*" 
              id="imagen-input"
              class="file-input-hidden"
            />
            <label for="imagen-input" class="upload-area" :class="{ 'has-image': previewUrl }">
              <div v-if="!previewUrl" class="upload-placeholder">
                <div class="upload-icon">📷</div>
                <p>Haz clic para seleccionar una imagen</p>
                <small>PNG, JPG hasta 5MB</small>
              </div>
              
              <div v-if="previewUrl" class="image-preview">
                <img :src="previewUrl" alt="Vista previa" class="preview-image" />
                <div class="image-overlay">
                  <span class="cambiar-texto">📷 Cambiar imagen</span>
                </div>
                <button 
                  type="button" 
                  class="btn-quitar-imagen" 
                  @click.prevent="quitarImagen"
                  title="Quitar imagen"
                >
                  ×
                </button>
              </div>
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-cancelar" @click="volverAtras">
            ❌ Cancelar
          </button>
          <button type="submit" class="btn-guardar" :disabled="!formularioValido">
            <span class="btn-icono">💾</span>
            <span>Guardar producto</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
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

// Estados para búsqueda y dropdowns
const busquedaCategoria = ref('')
const busquedaMarca = ref('')
const mostrarDropdownCategoria = ref(false)
const mostrarDropdownMarca = ref(false)
const categoriaSeleccionada = ref('')
const marcaSeleccionada = ref('')

// Obtener datos al iniciar
const obtenerCategorias = async () => {
  try {
    const res = await axios.get('/api/categorias')
    categorias.value = res.data
  } catch (error) {
    console.error('Error al obtener categorías:', error)
  }
}

const obtenerMarcas = async () => {
  try {
    const res = await axios.get('/api/marcas')
    marcas.value = res.data
  } catch (error) {
    console.error('Error al obtener marcas:', error)
  }
}

onMounted(() => {
  obtenerCategorias()
  obtenerMarcas()
  
  // Cerrar dropdowns al hacer clic fuera
  document.addEventListener('click', cerrarDropdowns)
})

// Crear nueva categoría
const crearCategoria = async () => {
  if (!nuevaCategoria.value.trim()) return
  try {
    await axios.post('/api/categorias', { nombre: nuevaCategoria.value })
    nuevaCategoria.value = ''
    obtenerCategorias()
    // Mostrar notificación de éxito
    mostrarNotificacion('Categoría creada exitosamente', 'success')
  } catch (error) {
    mostrarNotificacion('Error al crear categoría', 'error')
  }
}

// Crear nueva marca
const crearMarca = async () => {
  if (!nuevaMarca.value.trim()) return
  try {
    await axios.post('/api/marcas', { nombre: nuevaMarca.value })
    nuevaMarca.value = ''
    obtenerMarcas()
    mostrarNotificacion('Marca creada exitosamente', 'success')
  } catch (error) {
    mostrarNotificacion('Error al crear marca', 'error')
  }
}

// Formateo de precio
const precioFormateado = ref('')

const formatearPrecio = () => {
  let soloNumeros = precioFormateado.value.replace(/\D/g, '')
  let numero = parseInt(soloNumeros, 10)
  if (isNaN(numero)) {
    producto.value.precio = 0
    precioFormateado.value = ''
    return
  }
  producto.value.precio = numero
  precioFormateado.value = numero.toLocaleString('es-CO')
}

watch(() => producto.value.precio, (newVal) => {
  if (newVal !== null && newVal !== undefined) {
    precioFormateado.value = newVal.toLocaleString('es-CO')
  }
})

// Manejo de imagen
const imagenSeleccionada = ref(null)
const previewUrl = ref(null)

const handleImagenSeleccionada = (e) => {
  const file = e.target.files[0]
  if (file && file.type.startsWith('image/')) {
    imagenSeleccionada.value = file

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
        previewUrl.value = canvas.toDataURL('image/jpeg')
      }
      img.src = event.target.result
    }
    reader.readAsDataURL(file)
  }
}

const quitarImagen = () => {
  imagenSeleccionada.value = null
  previewUrl.value = null
  document.getElementById('imagen-input').value = ''
}

// Funciones de dropdown
const categoriasFiltradas = computed(() => {
  if (!busquedaCategoria.value) return categorias.value
  return categorias.value.filter(cat => 
    cat.nombre.toLowerCase().includes(busquedaCategoria.value.toLowerCase())
  )
})

const marcasFiltradas = computed(() => {
  if (!busquedaMarca.value) return marcas.value
  return marcas.value.filter(marca => 
    marca.nombre.toLowerCase().includes(busquedaMarca.value.toLowerCase())
  )
})

const toggleCategoriaDropdown = () => {
  mostrarDropdownCategoria.value = !mostrarDropdownCategoria.value
  mostrarDropdownMarca.value = false
}

const toggleMarcaDropdown = () => {
  mostrarDropdownMarca.value = !mostrarDropdownMarca.value
  mostrarDropdownCategoria.value = false
}

const seleccionarCategoria = (categoria) => {
  producto.value.categoria_id = categoria.id
  categoriaSeleccionada.value = categoria.nombre
  busquedaCategoria.value = ''
  mostrarDropdownCategoria.value = false
}

const seleccionarMarca = (marca) => {
  producto.value.marca_id = marca.id
  marcaSeleccionada.value = marca.nombre
  busquedaMarca.value = ''
  mostrarDropdownMarca.value = false
}

const filtrarCategorias = () => {
  mostrarDropdownCategoria.value = true
}

const filtrarMarcas = () => {
  mostrarDropdownMarca.value = true
}

const cerrarDropdowns = (e) => {
  if (!e.target.closest('.select-container')) {
    mostrarDropdownCategoria.value = false
    mostrarDropdownMarca.value = false
  }
}

// Validación del formulario
const formularioValido = computed(() => {
  return producto.value.nombre && 
         producto.value.precio > 0 && 
         producto.value.stock >= 0 && 
         producto.value.categoria_id && 
         producto.value.marca_id
})

// Navegación
const volverAtras = () => {
  router.go(-1)
}

// Enviar formulario
const crearProducto = async () => {
  if (!formularioValido.value) return
  
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
    mostrarNotificacion('Producto creado con éxito', 'success')
    // Redirigir después de un breve delay
    setTimeout(() => {
      router.push('/productos')
    }, 1500)
  } catch (error) {
    mostrarNotificacion('Error al crear producto', 'error')
    console.error(error)
  }
}

// Sistema de notificaciones simple
const mostrarNotificacion = (mensaje, tipo) => {
  // Implementación básica - se puede mejorar
  alert(mensaje)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
  box-sizing: border-box;
}

.crear-producto {
  font-family: 'Inter', sans-serif;
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem;
  background: linear-gradient(135deg, #f8fffe 0%, #f0f9f4 100%);
  min-height: 100vh;
}

/* Header */
.header-navegacion {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.btn-volver {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  border: 2px solid #99D7A9;
  color: #1a472a;
  padding: 0.8rem 1.5rem;
  border-radius: 50px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(153, 215, 169, 0.2);
}

.btn-volver:hover {
  background: #99D7A9;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(153, 215, 169, 0.3);
}

.icono-volver {
  font-size: 1.2rem;
  transition: transform 0.3s ease;
}

.btn-volver:hover .icono-volver {
  transform: translateX(-3px);
}

.titulo-seccion h2 {
  font-size: 2.2rem;
  font-weight: 700;
  color: #1a472a;
  margin: 0 0 0.5rem 0;
}

.subtitulo {
  color: #6b7280;
  font-size: 1rem;
  margin: 0;
}

/* Contenedor */
.contenedor-formulario {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Secciones admin */
.admin-sections {
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  overflow: hidden;
}

.admin-card {
  padding: 2rem;
  background: linear-gradient(135deg, #f8fffe 0%, #ecfdf5 100%);
  border-left: 4px solid #99D7A9;
}

.admin-card h3 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #1a472a;
  margin: 0 0 1.5rem 0;
}

.form-section {
  margin-bottom: 2rem;
}

.form-section:last-child {
  margin-bottom: 0;
}

.form-section label {
  display: block;
  font-weight: 600;
  color: #1a472a;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.input-group {
  display: flex;
  gap: 1rem;
  align-items: stretch;
}

.input-group input {
  flex: 1;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

.input-group input:focus {
  outline: none;
  border-color: #99D7A9;
  box-shadow: 0 0 0 3px rgba(153, 215, 169, 0.1);
}

.btn-crear {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #99D7A9 0%, #7bc96f 100%);
  color: white;
  border: none;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.btn-crear:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(153, 215, 169, 0.3);
}

.btn-crear:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-icono {
  font-size: 1.1rem;
}

/* Formulario principal */
.form-main {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-card h3 {
  font-size: 1.4rem;
  font-weight: 600;
  color: #1a472a;
  margin: 0 0 1.5rem 0;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f3f4f6;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group:last-child {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #1a472a;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #fafafa;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #99D7A9;
  background: white;
  box-shadow: 0 0 0 3px rgba(153, 215, 169, 0.1);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

/* Precio input especial */
.precio-input {
  position: relative;
  display: flex;
  align-items: center;
}

.moneda {
  position: absolute;
  left: 1rem;
  font-weight: 600;
  color: #1a472a;
  z-index: 1;
}

.precio-input input {
  padding-left: 2.5rem;
}

/* Selects personalizados */
.select-container {
  position: relative;
}

.select-wrapper {
  position: relative;
  cursor: pointer;
}

.select-input {
  cursor: pointer;
  padding-right: 3rem !important;
}

.dropdown-arrow {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
  transition: transform 0.3s ease;
  font-size: 0.8rem;
}

.dropdown-arrow.rotated {
  transform: translateY(-50%) rotate(180deg);
}

.dropdown-list {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 2px solid #99D7A9;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  max-height: 200px;
  overflow-y: auto;
  z-index: 100;
  animation: dropdownSlide 0.3s ease-out;
}

@keyframes dropdownSlide {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  padding: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 1px solid #f3f4f6;
}

.dropdown-item:last-child {
  border-bottom: none;
}

.dropdown-item:hover {
  background: #f0f9f4;
  color: #1a472a;
}

.dropdown-empty {
  padding: 1rem;
  text-align: center;
  color: #6b7280;
  font-style: italic;
}

/* Upload de imagen */
.imagen-upload {
  margin-top: 1rem;
}

.file-input-hidden {
  display: none;
}

.upload-area {
  display: block;
  border: 3px dashed #99D7A9;
  border-radius: 16px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #f8fffe;
  position: relative;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-area:hover {
  border-color: #7bc96f;
  background: #ecfdf5;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.upload-icon {
  font-size: 3rem;
  color: #99D7A9;
}

.upload-placeholder p {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a472a;
  margin: 0;
}

.upload-placeholder small {
  color: #6b7280;
}

.image-preview {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 12px;
  overflow: hidden;
}

.preview-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 12px;
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.upload-area:hover .image-overlay {
  opacity: 1;
}

.cambiar-texto {
  color: white;
  font-weight: 600;
}

.btn-quitar-imagen {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: #ef4444;
  color: white;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.2rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.btn-quitar-imagen:hover {
  background: #dc2626;
  transform: scale(1.1);
}

/* Botones de acción */
.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-cancelar,
.btn-guardar {
  flex: 1;
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-cancelar {
  background: #f3f4f6;
  color: #6b7280;
}

.btn-cancelar:hover {
  background: #e5e7eb;
  color: #374151;
}

.btn-guardar {
  background: linear-gradient(135deg, #99D7A9 0%, #7bc96f 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(153, 215, 169, 0.3);
}

.btn-guardar:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(153, 215, 169, 0.4);
}

.btn-guardar:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 2px solid #f3f4f6;
}

.btn-primary,
.btn-secondary {
  flex: 1;
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.btn-primary {
  background: linear-gradient(135deg, #99D7A9 0%, #7bc96f 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(153, 215, 169, 0.3);
}

.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(153, 215, 169, 0.4);
}

.btn-secondary {
  background: #f3f4f6;
  color: #6b7280;
}

.btn-secondary:hover {
  background: #e5e7eb;
  color: #374151;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .crear-producto {
    padding: 1rem;
  }
  
  .header-section h2 {
    font-size: 2rem;
  }
  
  .admin-sections {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .input-group {
    flex-direction: column;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .file-input-label {
    flex-direction: column;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .header-section h2 {
    font-size: 1.8rem;
  }
  
  .form-container {
    padding: 1.5rem;
  }
  
  .admin-card {
    padding: 1rem;
  }
}
</style>