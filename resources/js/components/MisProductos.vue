<template>

        <button class="btn-volver" @click="volverAtras">
        <span class="icono-volver">←</span>
        <span>Volver</span>
      </button>

  <div class="mis-productos">
    <div class="encabezado">
      <h2>Mis Productos</h2>
      <button class="btn-anadir" @click="añadirProducto">+ Añadir producto</button>
    </div>

    <div v-if="productos.length === 0" class="estado-vacio">
      <div class="icono-vacio">📦</div>
      <p>No tienes productos registrados.</p>
      <small>¡Comienza añadiendo tu primer producto!</small>
    </div>

    <div class="productos-grid">
      <div v-for="prod in productos" :key="prod.id" class="producto-card">
        <div class="producto-imagen-container">
          <img v-if="prod.imagen_url" :src="prod.imagen_url" class="producto-imagen" />
          <div v-else class="imagen-placeholder">
            <span>📷</span>
          </div>
          <div class="overlay-acciones">
            <button class="btn-accion btn-editar" @click="prepararEdicion(prod)" title="Editar">
              <span>✏️</span>
            </button>
            <button class="btn-accion btn-eliminar" @click="eliminarProducto(prod.id)" title="Eliminar">
              <span>🗑️</span>
            </button>
          </div>
        </div>
        
        <div class="producto-info">
          <h3>{{ prod.nombre }}</h3>
          <p class="categoria">{{ prod.categoria?.nombre }}</p>
          <div class="precio-stock">
            <p class="precio">{{ formatearPrecio(prod.precio) }}</p>
            <div class="stock-badge">
              <span class="stock-texto">Stock: {{ prod.stock }}</span>
              <div class="stock-indicator" :class="getStockClass(prod.stock)"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="productoEditar" class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h3>✨ Editar Producto</h3>
          <button type="button" class="btn-cerrar" @click="productoEditar = null">×</button>
        </div>
        
        <form @submit.prevent="guardarEdicion" class="formulario-modal">
          <div class="campo">
            <label>🏷️ Nombre</label>
            <input v-model="productoEditar.nombre" required />
          </div>

          <div class="campo">
            <label>📝 Descripción</label>
            <textarea v-model="productoEditar.descripcion" rows="2" placeholder="Describe tu producto..."></textarea>
          </div>

          <div class="campo">
            <label>💰 Precio</label>
            <input
              type="text"
              :value="formatearInputPrecio(productoEditar.precio)"
              @input="actualizarPrecio($event.target.value)"
              placeholder="Precio"
              required
            />
          </div>

          <div class="campo">
            <label>📊 Stock</label>
            <input type="number" v-model.number="productoEditar.stock" min="0" required />
          </div>

          <div class="campo" v-if="productoEditar.imagen_url">
            <label>🖼️ Imagen actual</label>
            <div class="imagen-actual">
              <img :src="productoEditar.imagen_url" class="imagen-preview" />
              <button type="button" class="btn-eliminar-imagen" @click="productoEditar.eliminar_imagen = true; productoEditar.imagen_url = null">
                ❌ Eliminar imagen
              </button>
            </div>
          </div>

          <div class="campo">
            <label>📸 Nueva imagen</label>
            <div class="file-input-wrapper">
              <input type="file" @change="handleImagenSeleccionada" id="file-input" accept="image/*" />
              <label for="file-input" class="file-input-label">
                <span>📁 Seleccionar archivo</span>
              </label>
            </div>
            
            <!-- Previsualización de nueva imagen -->
            <div v-if="imagenNueva" class="imagen-nueva-preview">
              <div class="preview-header">
                <span class="preview-label">✨ Nueva imagen seleccionada:</span>
                <button type="button" class="btn-quitar-nueva" @click="imagenNueva = null" title="Quitar imagen">
                  ×
                </button>
              </div>
              <div class="preview-container">
                <img :src="obtenerURLPreview(imagenNueva)" class="imagen-preview-nueva" />
                <div class="preview-info">
                  <p class="nombre-archivo">{{ imagenNueva.name }}</p>
                  <p class="tamano-archivo">{{ formatearTamanoArchivo(imagenNueva.size) }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-botones">
            <button type="submit" class="btn-guardar">💾 Guardar</button>
            <button type="button" class="btn-cancelar" @click="productoEditar = null">❌ Cancelar</button>
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

// Función para determinar el estado del stock
const getStockClass = (stock) => {
  if (stock > 20) return 'stock-alto'
  if (stock > 5) return 'stock-medio'
  return 'stock-bajo'
}

// Función para obtener URL de previsualización de archivo
const obtenerURLPreview = (archivo) => {
  return URL.createObjectURL(archivo)
}

// Función para formatear el tamaño del archivo
const formatearTamanoArchivo = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const volverAtras = () => {
  router.go(-1)
}

onMounted(obtenerProductos)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
  box-sizing: border-box;
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

.mis-productos {
  font-family: 'Inter', sans-serif;
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  background: linear-gradient(135deg, #f8fffe 0%, #f0f9f4 100%);
  min-height: 100vh;
}

/* Encabezado */
.encabezado {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  padding: 0 1rem;
}

.encabezado h2 {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1a472a;
  margin: 0;
  position: relative;
}

.encabezado h2::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, #99D7A9, #7bc96f);
  border-radius: 2px;
  animation: slideIn 0.6s ease-out;
}

@keyframes slideIn {
  from { width: 0; }
  to { width: 60px; }
}

.btn-anadir {
  background: linear-gradient(135deg, #99D7A9 0%, #7bc96f 100%);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 8px 25px rgba(153, 215, 169, 0.3);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.btn-anadir::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.6s;
}

.btn-anadir:hover::before {
  left: 100%;
}

.btn-anadir:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 35px rgba(153, 215, 169, 0.4);
}

.btn-anadir:active {
  transform: translateY(-1px);
}

/* Estado vacío */
.estado-vacio {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 24px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.08);
  margin: 2rem auto;
  max-width: 500px;
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.icono-vacio {
  font-size: 4rem;
  margin-bottom: 1rem;
  animation: bounce 2s infinite;
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-10px); }
  60% { transform: translateY(-5px); }
}

.estado-vacio p {
  font-size: 1.2rem;
  color: #1a472a;
  margin: 1rem 0 0.5rem 0;
  font-weight: 500;
}

.estado-vacio small {
  color: #6b7280;
  font-size: 0.9rem;
}

/* Grid de productos */
.productos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  padding: 1rem 0;
}

.producto-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  animation: cardEnter 0.6s ease-out;
}

@keyframes cardEnter {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.producto-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 50px rgba(153, 215, 169, 0.2);
}

.producto-imagen-container {
  position: relative;
  height: 200px;
  overflow: hidden;
  background: linear-gradient(135deg, #f8fffe 0%, #e8f5e8 100%);
}

.producto-imagen {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.producto-card:hover .producto-imagen {
  transform: scale(1.05);
}

.imagen-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  font-size: 3rem;
  color: #99D7A9;
  background: linear-gradient(135deg, #f8fffe 0%, #e8f5e8 100%);
}

.overlay-acciones {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  gap: 0.5rem;
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.3s ease;
}

.producto-card:hover .overlay-acciones {
  opacity: 1;
  transform: translateY(0);
}

.btn-accion {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  font-size: 1rem;
}

.btn-editar {
  background: rgba(153, 215, 169, 0.9);
  color: white;
}

.btn-eliminar {
  background: rgba(239, 68, 68, 0.9);
  color: white;
}

.btn-accion:hover {
  transform: scale(1.1);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.producto-info {
  padding: 1.5rem;
}

.producto-info h3 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #1a472a;
  margin: 0 0 0.5rem 0;
  line-height: 1.3;
}

.categoria {
  color: #99D7A9;
  font-weight: 500;
  font-size: 0.9rem;
  margin: 0 0 1rem 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.precio-stock {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
}

.precio {
  font-size: 1.4rem;
  font-weight: 700;
  color: #1a472a;
  margin: 0;
}

.stock-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #f3f4f6;
  padding: 0.5rem 1rem;
  border-radius: 20px;
}

.stock-texto {
  font-size: 0.85rem;
  font-weight: 500;
  color: #6b7280;
}

.stock-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.stock-alto { background: #10b981; }
.stock-medio { background: #f59e0b; }
.stock-bajo { background: #ef4444; }

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 2rem;
  backdrop-filter: blur(5px);
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal {
  background: white;
  border-radius: 24px;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px rgba(0,0,0,0.2);
  animation: modalSlide 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes modalSlide {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 2rem 0 2rem;
  border-bottom: 1px solid #e5e7eb;
  margin-bottom: 2rem;
}

.modal-header h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a472a;
  margin: 0;
}

.btn-cerrar {
  background: none;
  border: none;
  font-size: 2rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-cerrar:hover {
  background: #f3f4f6;
  color: #374151;
}

.formulario-modal {
  padding: 0 2rem 2rem 2rem;
}

.campo {
  margin-bottom: 1.5rem;
}

.campo label {
  display: block;
  font-weight: 600;
  color: #1a472a;
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.campo input,
.campo textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #fafafa;
}

.campo input:focus,
.campo textarea:focus {
  outline: none;
  border-color: #99D7A9;
  background: white;
  box-shadow: 0 0 0 3px rgba(153, 215, 169, 0.1);
}

.imagen-actual {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f8fffe;
  border-radius: 12px;
  border: 2px dashed #99D7A9;
}

.imagen-preview {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

.btn-eliminar-imagen {
  background: #fee2e2;
  color: #dc2626;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.85rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-eliminar-imagen:hover {
  background: #fecaca;
}

.file-input-wrapper {
  position: relative;
}

.file-input-wrapper input[type="file"] {
  position: absolute;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}

.file-input-label {
  display: block;
  padding: 1rem;
  background: #f8fffe;
  border: 2px dashed #99D7A9;
  border-radius: 12px;
  text-align: center;
  color: #1a472a;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.file-input-label:hover {
  background: #ecfdf5;
  border-color: #7bc96f;
}

/* Previsualización de nueva imagen */
.imagen-nueva-preview {
  margin-top: 1rem;
  padding: 1rem;
  background: linear-gradient(135deg, #f0f9ff 0%, #f8fffe 100%);
  border-radius: 12px;
  border: 2px solid #99D7A9;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.preview-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.preview-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1a472a;
}

.btn-quitar-nueva {
  background: #fee2e2;
  color: #dc2626;
  border: none;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.2rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.btn-quitar-nueva:hover {
  background: #fecaca;
  transform: scale(1.1);
}

.preview-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.imagen-preview-nueva {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border: 2px solid white;
}

.preview-info {
  flex: 1;
}

.nombre-archivo {
  font-size: 0.9rem;
  font-weight: 500;
  color: #1a472a;
  margin: 0 0 0.25rem 0;
  word-break: break-all;
}

.tamano-archivo {
  font-size: 0.8rem;
  color: #6b7280;
  margin: 0;
}

.modal-botones {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-guardar,
.btn-cancelar {
  flex: 1;
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-guardar {
  background: linear-gradient(135deg, #99D7A9 0%, #7bc96f 100%);
  color: white;
  box-shadow: 0 8px 25px rgba(153, 215, 169, 0.3);
}

.btn-guardar:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(153, 215, 169, 0.4);
}

.btn-cancelar {
  background: #f3f4f6;
  color: #6b7280;
}

.btn-cancelar:hover {
  background: #e5e7eb;
  color: #374151;
}

/* Responsive */
@media (max-width: 768px) {
  .mis-productos {
    padding: 1rem;
  }
  
  .encabezado {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .encabezado h2 {
    font-size: 2rem;
  }
  
  .productos-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .modal {
    margin: 1rem;
    max-height: calc(100vh - 2rem);
  }
  
  .modal-botones {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .encabezado h2 {
    font-size: 1.8rem;
  }
  
  .btn-anadir {
    padding: 0.8rem 1.5rem;
    font-size: 0.9rem;
  }
  
  .producto-card {
    border-radius: 16px;
  }
  
  .modal {
    border-radius: 16px;
  }
}
</style>