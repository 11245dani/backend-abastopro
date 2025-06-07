<template>
  <div class="contenedor-carrito">
    <!-- Productos en el carrito -->
    <div class="seccion-productos">
      <h2 class="titulo">Carrito de Compras</h2>
      <div v-if="carrito.length === 0" class="texto-vacio">El carrito está vacío</div>
      <div v-else>
        <div v-for="(producto, index) in carrito" :key="producto.id" class="producto">
          <div class="producto-info">
            <img :src="getImagen(producto.imagen)" class="imagen-producto" alt="producto" />
            <div>
              <p class="nombre-producto">{{ producto.nombre }}</p>
              <p class="distribuidor">{{ producto.distribuidor || 'Distribuidor' }}</p>
              <div class="cantidad-input">
                <input
                  type="number"
                  v-model.number="producto.cantidad"
                  :min="1"
                  :max="producto.stock"
                  @change="validarCantidad(producto)"
                />
                <span v-if="producto.cantidad > producto.stock" class="error">
                  Solo hay {{ producto.stock }} disponibles.
                </span>
              </div>
            </div>
          </div>
          <div class="producto-precio">
            <p class="precio-total">{{ formatearPrecio(producto.precio * producto.cantidad) }}</p>
            <button class="eliminar" @click="eliminarProducto(index)">🗑 Eliminar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Resumen del pedido -->
    <div class="resumen">
      <h3 class="titulo">Resumen del pedido</h3>
      <div class="linea"><span>Subtotal</span><span>{{ formatearPrecio(subtotal) }}</span></div>
      <div class="linea"><span>Envío</span><span>{{ formatearPrecio(envio) }}</span></div>
      <div class="linea"><span>Impuestos</span><span>{{ formatearPrecio(impuestos) }}</span></div>
      <hr />
      <div class="linea total"><span>Total</span><span>{{ formatearPrecio(total) }}</span></div>

      <label class="etiqueta" for="notas">Notas del pedido</label>
      <textarea
        id="notas"
        class="notas"
        placeholder="Instrucciones especiales para el distribuidor..."
        v-model="notas"
      ></textarea>

      <p class="etiqueta">Método de pago</p>
      <div class="metodo-pago">
        <label><input type="radio" name="metodo_pago" value="tarjeta" v-model="metodoPago" /> Tarjeta de crédito</label>
        <label><input type="radio" name="metodo_pago" value="transferencia" v-model="metodoPago" /> Transferencia bancaria</label>
      </div>

      <button class="boton-pago" @click="procederAlPago" :disabled="carrito.length === 0">
        Proceder al pago
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const carrito = ref([])
const notas = ref('')
const metodoPago = ref('tarjeta')

onMounted(() => {
  const guardado = localStorage.getItem('carrito')
  carrito.value = guardado ? JSON.parse(guardado) : []
  // Normalmente querrás validar que cada producto tenga las propiedades necesarias
})

function guardarCarrito() {
  localStorage.setItem('carrito', JSON.stringify(carrito.value))
}

function validarCantidad(producto) {
  if (producto.cantidad > producto.stock) {
    producto.cantidad = producto.stock
    mostrarError(`Stock insuficiente: solo quedan ${producto.stock} unidades.`)
  } else if (producto.cantidad < 1) {
    producto.cantidad = 1
  }
  guardarCarrito()
}

function mostrarError(mensaje) {
  alert(mensaje) // Puedes reemplazar por un componente toast
}

function eliminarProducto(index) {
  carrito.value.splice(index, 1)
  guardarCarrito()
}

const getImagen = (ruta) => {
  return ruta || '/img/producto_default.jpg'
}

async function procederAlPago() {
  if (carrito.value.some(p => p.cantidad > p.stock)) {
    mostrarError('Hay productos con cantidad mayor al stock disponible.')
    return
  }

  try {
    const productosParaEnviar = carrito.value.map(p => ({
      id: p.id,
      cantidad: p.cantidad
    }))

    const response = await axios.post(
      '/api/carrito/confirmar',
      { productos: productosParaEnviar, notas: notas.value, metodo_pago: metodoPago.value },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    )

    alert(response.data.message || 'Pedido confirmado correctamente.')
    carrito.value = []
    localStorage.removeItem('carrito')
    router.push('/mis-pedidos')
  } catch (error) {
    console.error('Error al confirmar el pedido:', error)
    alert(error.response?.data?.message || 'Ocurrió un error al confirmar el pedido.')
  }
}

const subtotal = computed(() =>
  carrito.value.reduce((sum, p) => sum + p.precio * p.cantidad, 0)
)
const envio = computed(() => 10000) // Puedes hacer dinámico este valor
const impuestos = computed(() => subtotal.value * 0.1)
const total = computed(() => subtotal.value + envio.value + impuestos.value)

const formatearPrecio = (precio) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(precio)
}
</script>


<style scoped>
.contenedor-carrito {
  display: flex;
  gap: 2rem;
  padding: 2rem;
  flex-wrap: wrap;
}

.seccion-productos, .resumen {
  background: #fff;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.seccion-productos {
  flex: 2;
}

.resumen {
  flex: 1;
  min-width: 280px;
}

.producto {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}

.producto-info {
  display: flex;
  gap: 1rem;
}

.imagen-producto {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 8px;
  background: #f2f2f2;
}

.nombre-producto {
  font-weight: bold;
  margin: 0;
}

.distribuidor {
  font-size: 0.9rem;
  color: #666;
}

.cantidad {
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.cantidad button {
  background: #eee;
  border: none;
  padding: 0.3rem 0.6rem;
  font-size: 1rem;
  cursor: pointer;
}

.producto-precio {
  text-align: right;
}

.precio-total {
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.eliminar {
  background: transparent;
  color: red;
  border: none;
  cursor: pointer;
}

.linea {
  display: flex;
  justify-content: space-between;
  margin: 0.5rem 0;
}

.total {
  font-weight: bold;
  font-size: 1.2rem;
}

.etiqueta {
  margin-top: 1rem;
  display: block;
  font-weight: bold;
}

.notas {
  width: 100%;
  height: 80px;
  margin-top: 0.5rem;
  resize: none;
}

.metodo-pago {
  margin-top: 0.5rem;
}

.boton-pago {
  width: 100%;
  margin-top: 1.5rem;
  background: #007bff;
  color: #fff;
  border: none;
  padding: 0.8rem;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}
</style>