<template>
  <transition name="fade">
    <div class="formulario-actualizar">
      <h2>✏️ Actualizar Información</h2>
      <p class="subtitulo">Modifica solo los campos necesarios</p>

      <form @submit.prevent="enviarFormulario" class="formulario">
        <div class="campo" :class="{ cambiado: form.nombre !== usuario.nombre }">
          <label for="nombre">🧑 Nombre:</label>
          <input id="nombre" v-model="form.nombre" />
        </div>

        <div class="campo" :class="{ cambiado: correoCambio }">
          <label for="correo">📧 Correo:</label>
          <input id="correo" type="email" v-model="form.correo" />
        </div>

        <div v-if="esTenderoOGestor" class="campo" :class="{ cambiado: form.direccion !== usuario.direccion }">
          <label for="direccion">🏠 Dirección:</label>
          <input id="direccion" v-model="form.direccion" />
        </div>

        <div v-if="esGestor" class="campo" :class="{ cambiado: form.nombre_empresa !== usuario.nombre_empresa }">
          <label for="empresa">🏢 Empresa:</label>
          <input id="empresa" v-model="form.nombre_empresa" />
        </div>

        <transition name="bounce">
          <div v-if="correoCambio" class="advertencia">
            ⚠️ Estás cambiando tu correo. La sesión se cerrará hasta verificar el nuevo correo.
          </div>
        </transition>

        <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
      </form>
    </div>
  </transition>
</template>

<script setup>
import { reactive, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const usuario = JSON.parse(localStorage.getItem('usuario'));

const form = reactive({
  nombre: usuario.nombre,
  correo: usuario.correo,
  direccion: usuario.direccion || '',
  nombre_empresa: usuario.nombre_empresa || ''
});

const esGestor = computed(() => usuario.rol === 'gestor_despacho');
const esTenderoOGestor = computed(() =>
  ['tendero', 'gestor_despacho'].includes(usuario.rol)
);

const correoCambio = computed(() =>
  form.correo && form.correo.toLowerCase() !== usuario.correo.toLowerCase()
);

const enviarFormulario = async () => {
  const datosActualizados = {};

  
  if (form.correo.toLowerCase() !== usuario.correo.toLowerCase()) datosActualizados.correo = form.correo;
  if (form.nombre !== usuario.nombre) datosActualizados.nombre = form.nombre;
  if (form.direccion !== usuario.direccion)
    datosActualizados.direccion = form.direccion;
  if (form.nombre_empresa !== usuario.nombre_empresa)
    datosActualizados.nombre_empresa = form.nombre_empresa;

  if (Object.keys(datosActualizados).length === 0) {
    alert('No se detectaron cambios para actualizar.');
    return;
  }

  try {
    const response = await axios.put(
      'http://localhost:8000/api/usuario/actualizar',
      datosActualizados,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );

    const { mensaje, requiere_verificacion, usuario: actualizado } = response.data;

    alert(mensaje);

    if (requiere_verificacion) {
      localStorage.removeItem('token');
      localStorage.removeItem('usuario');
      localStorage.removeItem('rol');
      router.push('/login');
    } else {
      localStorage.setItem('usuario', JSON.stringify(actualizado));
      router.push('/');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      console.error('Errores de validación:', error.response.data.errors);
      alert('Revisa los datos ingresados.');
    } else {
      console.error('Error inesperado:', error);
      alert('Ocurrió un error al actualizar la información.');
    }
  }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.bounce-enter-active {
  animation: bounceIn 0.6s;
}
@keyframes bounceIn {
  0% {
    transform: scale(0.8);
    opacity: 0;
  }
  60% {
    transform: scale(1.05);
    opacity: 1;
  }
  100% {
    transform: scale(1);
  }
}

.formulario-actualizar {
  padding: 30px;
  max-width: 600px;
  margin: auto;
  background: linear-gradient(to right, #f0f2f5, #dfe9f3);
  border-radius: 20px;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
  animation: slideIn 0.8s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateY(30px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

h2 {
  text-align: center;
  font-size: 28px;
  color: #34495e;
  margin-bottom: 10px;
}

.subtitulo {
  text-align: center;
  color: #666;
  margin-bottom: 25px;
  font-size: 15px;
}

form {
  display: flex;
  flex-direction: column;
}

.campo {
  margin-bottom: 18px;
  transition: all 0.3s ease;
}

.campo.cambiado {
  border-left: 5px solid #4a90e2;
  padding-left: 10px;
  background-color: #f0f8ff;
}

label {
  font-weight: 600;
  margin-bottom: 5px;
  display: block;
  color: #2c3e50;
}

input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
  transition: border 0.3s, box-shadow 0.3s;
}

input:focus {
  outline: none;
  border-color: #4a90e2;
  box-shadow: 0 0 8px rgba(74, 144, 226, 0.3);
  transform: scale(1.02);
}

.advertencia {
  margin-top: 15px;
  background-color: #fff3cd;
  color: #856404;
  padding: 12px;
  border-left: 5px solid #ffa502;
  border-radius: 8px;
  font-size: 15px;
  animation: fadeIn 0.5s ease-in-out;
}

.btn-guardar {
  padding: 12px;
  background-color: #4a90e2;
  color: white;
  font-weight: bold;
  font-size: 16px;
  border: none;
  border-radius: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-guardar:hover {
  background-color: #357ab8;
  transform: translateY(-2px);
}
</style>
