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
  margin: 40px auto;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  border-radius: 20px;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
  animation: slideIn 0.8s ease-out;
  border: 1px solid rgba(255, 255, 255, 0.3);
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
  color: #2c3e50;
  margin-bottom: 10px;
}

.subtitulo {
  text-align: center;
  color: #555;
  margin-bottom: 25px;
  font-size: 15px;
}

form {
  display: flex;
  flex-direction: column;
}

.campo {
  margin-bottom: 22px;
  transition: all 0.3s ease;
  padding: 10px 15px;
  border-radius: 14px;
  background: #f9f9f9;
  border: 1px solid #e0e0e0;
}

.campo.cambiado {
  border-left: 5px solid #4a90e2;
  background-color: #eef6ff;
}

label {
  font-weight: 600;
  margin-bottom: 5px;
  display: block;
  color: #2c3e50;
  font-size: 15px;
}

input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
  background-color: #fff;
  transition: border 0.3s, box-shadow 0.3s;
}

input:focus {
  outline: none;
  border-color: #4a90e2;
  box-shadow: 0 0 10px rgba(74, 144, 226, 0.2);
  background-color: #f4faff;
}

.advertencia {
  margin-top: 15px;
  background-color: #fff3cd;
  color: #856404;
  padding: 14px;
  border-left: 6px solid #ffa502;
  border-radius: 10px;
  font-size: 15px;
  animation: fadeIn 0.5s ease-in-out;
}

.btn-guardar {
  padding: 14px;
  background: linear-gradient(to right, #4a90e2, #357ab8);
  color: white;
  font-weight: bold;
  font-size: 17px;
  border: none;
  border-radius: 18px;
  cursor: pointer;
  transition: background 0.3s ease, transform 0.2s ease;
}

.btn-guardar:hover {
  background: linear-gradient(to right, #357ab8, #2d6db2);
  transform: translateY(-2px);
}

/* Responsive para móviles */
@media (max-width: 600px) {
  .formulario-actualizar {
    padding: 20px;
  }

  h2 {
    font-size: 22px;
  }

  .btn-guardar {
    font-size: 15px;
    padding: 12px;
  }
}
</style>