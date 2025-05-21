<template>
  <div class="contenedor-carrito">
    <!-- Productos en el carrito -->
    <div class="seccion-productos">
      <h2 class="titulo">Carrito de Compras</h2>
      <div v-if="carrito.length === 0" class="texto-vacio">El carrito está vacío</div>
      <div v-else>
        <div v-for="(producto, index) in carrito" :key="index" class="producto">
          <div class="producto-info">
            <div class="imagen-producto"></div>
            <div>
              <p class="nombre-producto">{{ producto.nombre }}</p>
              <p class="distribuidor">{{ producto.distribuidor || 'Distribuidor' }}</p>
              <div class="cantidad">
                <button @click="disminuirCantidad(index)">−</button>
                <span>{{ producto.cantidad }}</span>
                <button @click="aumentarCantidad(index)">+</button>
              </div>
            </div>
          </div>
          <div class="producto-precio">
            <p class="precio-total">${{ (producto.precio * producto.cantidad).toFixed(2) }}</p>
            <button class="eliminar" @click="eliminarProducto(index)">🗑 Eliminar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Resumen del pedido -->
    <div class="resumen">
      <h3 class="titulo">Resumen del pedido</h3>
      <div class="linea"><span>Subtotal</span><span>${{ subtotal.toFixed(2) }}</span></div>
      <div class="linea"><span>Envío</span><span>${{ envio.toFixed(2) }}</span></div>
      <div class="linea"><span>Impuestos</span><span>${{ impuestos.toFixed(2) }}</span></div>
      <hr />
      <div class="linea total"><span>Total</span><span>${{ total.toFixed(2) }}</span></div>

      <label class="etiqueta" for="notas">Notas del pedido</label>
      <textarea id="notas" class="notas" placeholder="Instrucciones especiales para el distribuidor..."></textarea>

      <p class="etiqueta">Método de pago</p>
      <div class="metodo-pago">
        <label><input type="radio" name="metodo_pago" value="tarjeta" checked /> Tarjeta de crédito</label>
        <label><input type="radio" name="metodo_pago" value="transferencia" /> Transferencia bancaria</label>
      </div>

        <button class="boton-pago" @click="procederAlPago">Proceder al pago</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'


const router = useRouter()

async function procederAlPago() {
  try {
    const response = await axios.post('/api/carrito/confirmar', {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })

    alert('Pedido confirmado correctamente.')
    
    // Vaciar carrito local
    carrito.value = []
    localStorage.removeItem('carrito')

    // Redirigir al historial de pedidos, gracias o similar
    router.push('/mis-pedidos')  // Asegúrate que esta ruta exista
  } catch (error) {
    console.error('Error al confirmar el pedido:', error)
    alert(error.response?.data?.message || 'Ocurrió un error al confirmar el pedido.')
  }
}

const carrito = ref([])

onMounted(() => {
  const guardado = localStorage.getItem('carrito')
  carrito.value = guardado ? JSON.parse(guardado) : []
})

function aumentarCantidad(index) {
  carrito.value[index].cantidad++
  guardarCarrito()
}

function disminuirCantidad(index) {
  if (carrito.value[index].cantidad > 1) {
    carrito.value[index].cantidad--
    guardarCarrito()
  }
}

function eliminarProducto(index) {
  carrito.value.splice(index, 1)
  guardarCarrito()
}

function guardarCarrito() {
  localStorage.setItem('carrito', JSON.stringify(carrito.value))
}

const subtotal = computed(() =>
  carrito.value.reduce((sum, p) => sum + p.precio * p.cantidad, 0)
)
const envio = computed(() => 10)
const impuestos = computed(() => subtotal.value * 0.1)
const total = computed(() => subtotal.value + envio.value + impuestos.value)
</script>

<style scoped>
.contenedor-carrito {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px;
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}
@media (min-width: 768px) {
  .contenedor-carrito {
    grid-template-columns: 2fr 1fr;
  }
}

.seccion-productos {
  display: flex;
  flex-direction: column;
}
.titulo {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 16px;
}
.texto-vacio {
  color: #6b7280;
}

.producto {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  border: 1px solid #e5e7eb;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 16px;
}
.producto-info {
  display: flex;
  gap: 16px;
}
.imagen-producto {
  width: 64px;
  height: 64px;
  background-color: #e5e7eb;
  border-radius: 8px;
}
.nombre-producto {
  font-weight: 500;
}
.distribuidor {
  font-size: 0.875rem;
  color: #6b7280;
}
.cantidad {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}
.cantidad button {
  padding: 4px 8px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  background: white;
  cursor: pointer;
}
.producto-precio {
  text-align: right;
}
.precio-total {
  font-weight: 600;
}
.eliminar {
  margin-top: 8px;
  font-size: 0.875rem;
  color: #ef4444;
  background: none;
  border: none;
  cursor: pointer;
}

.resumen {
  border: 1px solid #e5e7eb;
  padding: 24px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.linea {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}
.total {
  font-weight: bold;
  font-size: 1.1rem;
}
.etiqueta {
  font-size: 0.875rem;
  font-weight: 500;
  margin-top: 16px;
  margin-bottom: 4px;
}
.notas {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #d1d5db;
  font-size: 0.875rem;
}
.metodo-pago {
  margin-top: 8px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 0.875rem;
}
.boton-pago {
  margin-top: 24px;
  width: 100%;
  background-color: #000;
  color: #fff;
  padding: 10px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.boton-pago:hover {
  background-color: #111;
}
</style>
