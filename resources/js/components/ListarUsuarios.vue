<template>
  <div class="listar-usuarios">
    <h2>Lista de Usuarios</h2>

    <!-- Filtros y Búsqueda -->
    <div class="filtros">
      <input
        type="text"
        v-model="busqueda"
        placeholder="Buscar por nombre o correo..."
      />

      <select v-model="filtroRol">
        <option value="">Todos los roles</option>
        <option value="tendero">Tendero</option>
        <option value="gestor_despacho">Distribuidor</option>
        <option value="admin">Administrador</option>
      </select>

      <select v-model="filtroEstado">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="pendiente">Pendiente</option>
        <option value="rechazado">Rechazado</option>
      </select>

      <select v-model="filtroAutorizacion">
        <option value="">Todas las autorizaciones</option>
        <option value="aprobado">Aprobado</option>
        <option value="pendiente">Pendiente</option>
        <option value="rechazado">Rechazado</option>
        <option value="No aplica">No aplica</option>
      </select>
    </div>

    <!-- Tabla de usuarios -->
    <table>
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Tipo</th>
          <th>Estado</th>
          <th>Autorización distribución</th> 
          <th>Fecha de Registro</th>
          <th>Contacto</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="usuario in usuariosFiltrados" :key="usuario.id">
          <td>{{ usuario.nombre }}</td>
          <td>{{ mostrarRol(usuario.rol) }}</td>
          <td>{{ usuario.estado }}</td>
          <td>
            <span
              :class="{
                'badge badge-aprobado': usuario.distribuidor?.estado_autorizacion === 'aprobado',
                'badge badge-pendiente': usuario.distribuidor?.estado_autorizacion === 'pendiente',
                'badge badge-rechazado': usuario.distribuidor?.estado_autorizacion === 'rechazado',
                'badge badge-desconocido': !usuario.distribuidor?.estado_autorizacion
              }"
            >
              {{ usuario.distribuidor?.estado_autorizacion || 'No aplica' }}
            </span>
          </td>
          <td>{{ formatearFecha(usuario.created_at) }}</td>
          <td>{{ usuario.correo }}<br />{{ usuario.telefono || 'No disponible' }}</td>
          <td>
            <div v-if="usuario.rol === 'gestor_despacho' && usuario.distribuidor?.estado_autorizacion !== 'aprobado'">
              <button @click="aprobarGestor(usuario)">Aprobar</button>
              <button @click="rechazarGestor(usuario)" class="rechazar">Rechazar</button>
            </div>
            <div v-else>
              <button @click="verUsuario(usuario)">Ver</button>
            </div>
          </td>
        </tr>
        <tr v-if="usuariosFiltrados.length === 0">
          <td colspan="7">No hay usuarios que coincidan con los criterios.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

axios.defaults.baseURL = 'http://127.0.0.1:8000';

const usuarios = ref([]);
const busqueda = ref('');
const filtroRol = ref('');
const filtroEstado = ref('');
const filtroAutorizacion = ref('');

onMounted(async () => {
  try {
    const response = await axios.get('/api/usuarios', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
    usuarios.value = response.data.usuarios;
  } catch (error) {
    console.error(error);
    alert('Error al obtener usuarios');
  }
});

const usuariosFiltrados = computed(() => {
  return usuarios.value.filter((usuario) => {
    const coincideBusqueda =
      usuario.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) ||
      usuario.correo.toLowerCase().includes(busqueda.value.toLowerCase());

    const coincideRol = filtroRol.value ? usuario.rol === filtroRol.value : true;
    const coincideEstado = filtroEstado.value ? usuario.estado === filtroEstado.value : true;

    const estadoAutorizacion = usuario.distribuidor?.estado_autorizacion || 'No aplica';
    const coincideAutorizacion = filtroAutorizacion.value
      ? estadoAutorizacion === filtroAutorizacion.value
      : true;

    return coincideBusqueda && coincideRol && coincideEstado && coincideAutorizacion;
  });
});

const mostrarRol = (rol) => {
  if (rol === 'tendero') return 'Tienda';
  if (rol === 'gestor_despacho') return 'Distribuidor';
  if (rol === 'admin') return 'Administrador';
  return rol;
};

const formatearFecha = (fecha) => {
  const date = new Date(fecha);
  return date.toLocaleDateString('es-CO');
};

const verUsuario = (usuario) => {
  alert(`Ver detalles de: ${usuario.nombre}`);
};

const aprobarGestor = async (usuario) => {
  const token = localStorage.getItem('token');
  if (!confirm(`¿Estás seguro de aprobar a ${usuario.nombre}?`)) return;

  try {
    await axios.put(`/api/admin/aprobar-gestor-despacho/${usuario.id}`, {}, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    });

    if (usuario.distribuidor) {
      usuario.distribuidor.estado_autorizacion = 'aprobado';
    }

    alert('Gestor de despacho aprobado exitosamente');
  } catch (error) {
    console.error(error);
    alert('Error al aprobar al gestor de despacho');
  }
};

const rechazarGestor = async (usuario) => {
  const token = localStorage.getItem('token');
  if (!confirm(`¿Estás seguro de rechazar a ${usuario.nombre}?`)) return;

  try {
    await axios.put(`/api/admin/rechazar-gestor-despacho/${usuario.id}`, {}, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    });

    if (usuario.distribuidor) {
      usuario.distribuidor.estado_autorizacion = 'rechazado';
    }

    alert('Gestor de despacho rechazado exitosamente');
  } catch (error) {
    console.error(error);
    alert('Error al rechazar al gestor de despacho');
  }
};
</script>

<style scoped>
.listar-usuarios {
  padding: 24px;
  background-color: #f9f9f9;
  border-radius: 10px;
  font-family: 'Segoe UI', sans-serif;
}

h2 {
  color: #333;
  margin-bottom: 16px;
}

.filtros {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.filtros input,
.filtros select {
  padding: 8px 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background-color: #fff;
  font-size: 14px;
  transition: border-color 0.3s ease;
}


.filtros input:focus,
.filtros select:focus {
  outline: none;
  border-color: #99D7A9;
}

table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

th, td {
  padding: 12px 16px;
  border-bottom: 1px solid #eee;
  text-align: left;
}

th {
  background-color: #e9f5ec;
  color: #333;
  font-weight: 600;
}

button {
  padding: 6px 12px;
  background-color: #99D7A9;
  color: #fff;
  border: none;
  border-radius: 5px;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
}

button:hover {
  background-color: #85c395;
}

button.rechazar {
  background-color: #d32f2f;
}

button.rechazar:hover {
  background-color: #b71c1c;
}

.badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #fff;
  display: inline-block;
  text-transform: capitalize;
}

.badge-aprobado {
  background-color: #99D7A9;
}

.badge-pendiente {
  background-color: #fbc02d;
}

.badge-rechazado {
  background-color: #f44336;
}

.badge-desconocido {
  background-color: #9e9e9e;
}

table td[colspan="7"] {
  text-align: center;
  color: #777;
  padding: 20px;
}
</style>