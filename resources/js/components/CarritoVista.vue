<template>
  <div class="contenedor-carrito">
    <!-- Productos en el carrito -->
    <div class="seccion-productos">
      <h2 class="titulo">
        <span class="titulo-texto">Carrito de Compras</span>
        <span class="contador-productos" v-if="carrito.length > 0">{{ carrito.length }}</span>
      </h2>
      
      <transition name="fade-empty" mode="out-in">
        <div v-if="carrito.length === 0" class="estado-vacio">
          <svg class="icono-carrito-vacio" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
          </svg>
          <p class="texto-vacio">Tu carrito está vacío</p>
          <router-link to="/catalogo" class="boton-seguir-comprando">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icono-flecha">
              <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
            </svg>
            Seguir comprando
          </router-link>
        </div>
        
        <div v-else>
          <transition-group name="lista-productos" tag="div">
            <div v-for="(producto, index) in carrito" :key="`${producto.id}-${index}`" class="producto">
              <div class="producto-info">
                <div class="contenedor-imagen">
                  <transition name="zoom" mode="out-in">
                    <img :src="getImagen(producto.imagen)" class="imagen-producto" alt="producto" />
                  </transition>
                </div>
                <div class="detalles-producto">
                  <p class="nombre-producto">{{ producto.nombre }}</p>
                  <p class="distribuidor">{{ producto.distribuidor || 'Distribuidor' }}</p>
                  
                  <div class="contador-cantidad">
                    <button class="boton-cantidad" @click="decrementarCantidad(producto)" :disabled="producto.cantidad <= 1">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 13H5v-2h14v2z"/>
                      </svg>
                    </button>
                    <input
                      type="number"
                      v-model.number="producto.cantidad"
                      :min="1"
                      :max="producto.stock"
                      @change="validarCantidad(producto)"
                      @blur="guardarCarrito"
                      class="input-cantidad"
                    />
                    <button class="boton-cantidad" @click="incrementarCantidad(producto)" :disabled="producto.cantidad >= producto.stock">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                      </svg>
                    </button>
                  </div>
                  
                  <transition name="slide-fade">
                    <div v-if="producto.cantidad > producto.stock" class="mensaje-error">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icono-error">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                      </svg>
                      Solo hay {{ producto.stock }} disponibles
                    </div>
                  </transition>
                </div>
              </div>
              
              <div class="producto-precio">
                <p class="precio-total">{{ formatearPrecio(producto.precio * producto.cantidad) }}</p>
                <button class="eliminar" @click="eliminarProducto(index)">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icono-eliminar">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                  </svg>
                </button>
              </div>
            </div>
          </transition-group>
        </div>
      </transition>
    </div>

    <!-- Resumen del pedido -->
    <transition name="slide-left" appear>
      <div class="resumen" v-if="carrito.length > 0">
        <h3 class="titulo-resumen">Resumen del pedido</h3>
        
        <div class="detalle-resumen">
          <div class="linea">
            <span>Subtotal</span>
            <span>{{ formatearPrecio(subtotal) }}</span>
          </div>
          <div class="linea">
            <span>Envío</span>
            <span class="envio-gratis">¡Gratis!</span>
          </div>
          
          <div class="separador"></div>
          
          <div class="linea total">
            <span>Total</span>
            <span class="precio-total">{{ formatearPrecio(total) }}</span>
          </div>
        </div>

        <div class="campo-notas">
          <label class="etiqueta" for="notas">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icono-notas">
              <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
            Notas del pedido
          </label>
          <textarea
            id="notas"
            class="notas"
            placeholder="Instrucciones especiales para el distribuidor..."
            v-model="notas"
            @focus="expandirTextarea"
            @blur="contraerTextarea"
          ></textarea>
        </div>

        <div class="metodo-pago">
          <div class="tarjeta-metodo-pago">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icono-pago">
              <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
            </svg>
            <div>
              <p class="titulo-metodo-pago">Método de pago</p>
              <p class="valor-metodo-pago">Contra entrega</p>
            </div>
          </div>
        </div>

        <div class="mensaje-info">
          <p class="texto-explicacion">
            El pedido será enviado al distribuidor correspondiente, quien tiene la facultad de aceptarlo. 
            Una vez aprobado, se descontará el stock de los productos y se iniciará el proceso de envío.
          </p>
        </div>

        <button 
          class="boton-pago" 
          @click="procederAlPago" 
          :disabled="carrito.length === 0 || carrito.some(p => p.cantidad > p.stock)"
          :class="{ 'boton-cargando': cargando }"
        >
          <span class="texto-boton">
            <span v-if="!cargando">Confirmar pedido</span>
            <span v-else>Procesando...</span>
          </span>
          <span class="efecto-hover"></span>
          <span class="efecto-click"></span>
        </button>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const carrito = ref([])
const notas = ref('')
const metodoPago = ref('contraentrega')
const cargando = ref(false)

onMounted(() => {
  const guardado = localStorage.getItem('carrito')
  carrito.value = guardado ? JSON.parse(guardado) : []
})

function guardarCarrito() {
  localStorage.setItem('carrito', JSON.stringify(carrito.value))
}

function incrementarCantidad(producto) {
  if (producto.cantidad < producto.stock) {
    producto.cantidad++
    guardarCarrito()
  }
}

function decrementarCantidad(producto) {
  if (producto.cantidad > 1) {
    producto.cantidad--
    guardarCarrito()
  }
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
  // Podríamos implementar un toast notification aquí
  alert(mensaje)
}

function eliminarProducto(index) {
  carrito.value.splice(index, 1)
  guardarCarrito()
}

function expandirTextarea(e) {
  e.target.style.height = '120px'
}

function contraerTextarea(e) {
  if (!e.target.value) {
    e.target.style.height = '80px'
  }
}

const getImagen = (ruta) => ruta || '/img/producto_default.jpg'

async function procederAlPago() {
  if (carrito.value.some(p => p.cantidad > p.stock)) {
    mostrarError('Hay productos con cantidad mayor al stock disponible.')
    return
  }

  cargando.value = true

  try {
    const productosParaEnviar = carrito.value.map(p => ({
      id: p.id,
      cantidad: p.cantidad
    }))

    const response = await axios.post(
      '/api/carrito/confirmar',
      {
        productos: productosParaEnviar,
        notas: notas.value,
        metodo_pago: metodoPago.value
      },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    )

    // Animación de éxito antes de redirigir
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    carrito.value = []
    localStorage.removeItem('carrito')
    router.push('/mis-pedidos')
  } catch (error) {
    console.error('Error al confirmar el pedido:', error)
    mostrarError(error.response?.data?.message || 'Ocurrió un error al confirmar el pedido.')
  } finally {
    cargando.value = false
  }
}

const subtotal = computed(() =>
  carrito.value.reduce((sum, p) => sum + p.precio * p.cantidad, 0)
)
const total = computed(() => subtotal.value)

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
/* Variables de color */
:root {
  --color-primario: #99D7A9;
  --color-primario-oscuro: #7dbe8d;
  --color-secundario: #F8F9FA;
  --color-texto: #2D3748;
  --color-texto-secundario: #718096;
  --color-borde: #E2E8F0;
  --color-fondo: #FFFFFF;
  --color-error: #E53E3E;
  --color-exito: #38A169;
  --sombra: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --sombra-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --transicion-rapida: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  --transicion-media: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Estilos base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
  color: var(--color-texto);
  background-color: #f5f7fa;
  line-height: 1.6;
}

/* Contenedor principal */
.contenedor-carrito {
  display: flex;
  gap: 2rem;
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  flex-wrap: wrap;
}

/* Sección de productos */
.seccion-productos {
  flex: 2;
  min-width: 0;
  background: var(--color-fondo);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--sombra);
  transition: var(--transicion-media);
}

.seccion-productos:hover {
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

.titulo {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
  position: relative;
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--color-texto);
}

.titulo::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 4px;
  background: var(--color-primario);
  border-radius: 2px;
}

.contador-productos {
  background: var(--color-primario);
  color: white;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 600;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

/* Estado vacío */
.estado-vacio {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 0;
  text-align: center;
}

.icono-carrito-vacio {
  width: 80px;
  height: 80px;
  fill: var(--color-borde);
  margin-bottom: 1.5rem;
  opacity: 0.7;
}

.texto-vacio {
  font-size: 1.2rem;
  color: var(--color-texto-secundario);
  margin-bottom: 2rem;
}

.boton-seguir-comprando {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.5rem;
  background: var(--color-primario);
  color: black;
  border: none;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  transition: var(--transicion-rapida);
  box-shadow: 0 4px 6px rgba(153, 215, 169, 0.3);
}

.boton-seguir-comprando:hover {
  background: var(--color-primario-oscuro);
  transform: translateY(-2px);
  box-shadow: 0 6px 8px rgba(153, 215, 169, 0.4);
}

.boton-seguir-comprando:active {
  transform: translateY(0);
}

.icono-flecha {
  width: 18px;
  height: 18px;
  fill: black;
}

/* Lista de productos */
.lista-productos {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.producto {
  display: flex;
  justify-content: space-between;
  padding: 1.5rem;
  border-radius: 12px;
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: var(--transicion-rapida);
  position: relative;
  overflow: hidden;
}

.producto:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.producto::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: var(--color-primario);
  transition: var(--transicion-rapida);
}

.producto:hover::after {
  width: 6px;
}

.producto-info {
  display: flex;
  gap: 1.5rem;
  flex: 1;
}

.contenedor-imagen {
  width: 100px;
  height: 100px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
}

.imagen-producto {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transicion-media);
}

.producto:hover .imagen-producto {
  transform: scale(1.05);
}

.detalles-producto {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  flex: 1;
}

.nombre-producto {
  font-weight: 700;
  font-size: 1.1rem;
  margin-bottom: 0.25rem;
  color: var(--color-texto);
}

.distribuidor {
  font-size: 0.9rem;
  color: var(--color-texto-secundario);
  margin-bottom: 1rem;
}

.contador-cantidad {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.boton-cantidad {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid var(--color-borde);
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transicion-rapida);
}

.boton-cantidad:hover {
  background: #f0fdf4;
  border-color: var(--color-primario);
}

.boton-cantidad:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: #f8fafc;
}

.boton-cantidad svg {
  width: 16px;
  height: 16px;
  fill: var(--color-primario);
}

.input-cantidad {
  width: 50px;
  height: 32px;
  text-align: center;
  border: 1px solid var(--color-borde);
  border-radius: 8px;
  font-weight: 600;
  transition: var(--transicion-rapida);
}

.input-cantidad:focus {
  outline: none;
  border-color: var(--color-primario);
  box-shadow: 0 0 0 2px rgba(153, 215, 169, 0.3);
}

.mensaje-error {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  color: var(--color-error);
  margin-top: 0.5rem;
  animation: shake 0.5s;
}

.icono-error {
  width: 16px;
  height: 16px;
  fill: var(--color-error);
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-3px); }
  40%, 80% { transform: translateX(3px); }
}

.producto-precio {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: space-between;
  min-width: 120px;
  margin-left: 1rem;
}

.precio-total {
  font-weight: 700;
  font-size: 1.1rem;
  color: var(--color-texto);
}

.eliminar {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: var(--transicion-rapida);
  display: flex;
  align-items: center;
  justify-content: center;
}

.eliminar:hover {
  background: #fee2e2;
}

.icono-eliminar {
  width: 20px;
  height: 20px;
  fill: var(--color-error);
  transition: var(--transicion-rapida);
}

.eliminar:hover .icono-eliminar {
  transform: rotate(15deg);
}

/* Sección de resumen */
.resumen {
  flex: 1;
  min-width: 320px;
  background: var(--color-fondo);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--sombra);
  align-self: flex-start;
  position: sticky;
  top: 2rem;
}

.titulo-resumen {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  position: relative;
  color: var(--color-texto);
}

.titulo-resumen::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 60px;
  height: 4px;
  background: var(--color-primario);
  border-radius: 2px;
}

.detalle-resumen {
  margin-bottom: 1.5rem;
}

.linea {
  display: flex;
  justify-content: space-between;
  margin: 1rem 0;
  font-size: 0.95rem;
}

.linea.total {
  margin: 1.5rem 0;
  font-size: 1.2rem;
  font-weight: 700;
}

.precio-total {
  color: var(--color-primario);
  font-weight: 800;
}

.envio-gratis {
  color: var(--color-exito);
  font-weight: 600;
}

.separador {
  height: 1px;
  background: var(--color-borde);
  margin: 1rem 0;
}

.campo-notas {
  margin: 2rem 0;
}

.etiqueta {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
  color: var(--color-texto);
}

.icono-notas {
  width: 18px;
  height: 18px;
  fill: var(--color-primario);
}

.notas {
  width: 100%;
  height: 80px;
  padding: 1rem;
  border: 1px solid var(--color-borde);
  border-radius: 10px;
  resize: none;
  font-family: inherit;
  transition: var(--transicion-media);
  background: #f8fafc;
}

.notas:focus {
  outline: none;
  border-color: var(--color-primario);
  box-shadow: 0 0 0 3px rgba(153, 215, 169, 0.3);
  background: white;
}

.metodo-pago {
  margin: 1.5rem 0;
}

.tarjeta-metodo-pago {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid var(--color-borde);
}

.icono-pago {
  width: 24px;
  height: 24px;
  fill: var(--color-primario);
}

.titulo-metodo-pago {
  font-size: 0.85rem;
  color: var(--color-texto-secundario);
}

.valor-metodo-pago {
  font-weight: 600;
  color: var(--color-texto);
}

.mensaje-info {
  font-size: 0.85rem;
  color: var(--color-texto-secundario);
  margin: 1.5rem 0;
  line-height: 1.5;
}

/* Botón de pago */
.boton-pago {
  position: relative;
  width: 100%;
  padding: 1.25rem;
  background: var(--color-primario);
  color: black;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1.1rem;
  cursor: pointer;
  overflow: hidden;
  transition: var(--transicion-rapida);
  box-shadow: 0 4px 6px rgba(153, 215, 169, 0.3);
  z-index: 1;
}

.boton-pago:hover:not(:disabled) {
  background: var(--color-primario-oscuro);
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(153, 215, 169, 0.4);
}

.boton-pago:active:not(:disabled) {
  transform: translateY(0);
}

.boton-pago:disabled {
  background: #cbd5e0;
  cursor: not-allowed;
  box-shadow: none;
}

.efecto-hover {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
  opacity: 0;
  transition: var(--transicion-rapida);
  z-index: -1;
}

.boton-pago:hover .efecto-hover {
  opacity: 1;
}

.efecto-click {
  position: absolute;
  width: 20px;
  height: 20px;
  background: rgba(255, 255, 255, 0.4);
  border-radius: 50%;
  transform: scale(0);
  pointer-events: none;
  z-index: -1;
}

.boton-pago:active .efecto-click {
  animation: ripple 0.6s ease-out;
}

@keyframes ripple {
  to {
    transform: scale(10);
    opacity: 0;
  }
}

.boton-cargando {
  pointer-events: none;
}

.boton-cargando::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Transiciones y animaciones */
.fade-empty-enter-active,
.fade-empty-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-empty-enter-from,
.fade-empty-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.lista-productos-move {
  transition: transform 0.5s ease;
}

.lista-productos-enter-active,
.lista-productos-leave-active {
  transition: all 0.5s ease;
}

.lista-productos-enter-from,
.lista-productos-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.zoom-enter-active,
.zoom-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.zoom-enter-from,
.zoom-leave-to {
  transform: scale(0.9);
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(10px);
  opacity: 0;
}

.slide-left-enter-active {
  transition: all 0.4s ease-out;
}

.slide-left-enter-from {
  transform: translateX(20px);
  opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .contenedor-carrito {
    flex-direction: column;
    padding: 1rem;
  }
  
  .seccion-productos, .resumen {
    width: 100%;
  }
  
  .resumen {
    position: static;
  }
}
</style>