<template>
  <div class="informes">
    <!-- Header con gradiente -->
    <div class="header-section">
      <h1 class="titulo">
        <i class="icon">📊</i>
        Informes y Análisis de Ventas
      </h1>
      <p class="subtitulo">Dashboard de métricas y rendimiento</p>
    </div>

    <!-- Filtros con diseño moderno -->
    <div class="filtros-container">
      <div class="filtros-card">
        <div class="filtros-header">
          <i class="filter-icon">🔍</i>
          <span>Filtrar por Período</span>
        </div>
        <div class="filtros">
          <div class="input-group">
            <label>Fecha Inicio</label>
            <input type="date" v-model="fechaInicio" class="date-input" />
          </div>
          <div class="input-group">
            <label>Fecha Fin</label>
            <input type="date" v-model="fechaFin" class="date-input" />
          </div>
          <button @click="obtenerInformes" class="filter-btn">
            <i class="btn-icon">⚡</i>
            Aplicar Filtros
          </button>
        </div>
      </div>
    </div>

    <!-- KPIs con animaciones -->
    <div class="kpis-section">
      <h2 class="section-title">Métricas Principales</h2>
      <div class="kpis">
        <div class="kpi kpi-pedidos" :key="informes.total_pedidos">
          <div class="kpi-icon">📦</div>
          <div class="kpi-content">
            <h3>Pedidos Aceptados</h3>
            <p class="kpi-value">{{ informes.total_pedidos }}</p>
          </div>
          <div class="kpi-decoration"></div>
        </div>
        
        <div class="kpi kpi-pendientes" :key="informes.total_pendientes">
          <div class="kpi-icon">⏳</div>
          <div class="kpi-content">
            <h3>Pedidos Pendientes</h3>
            <p class="kpi-value">{{ informes.total_pendientes }}</p>
          </div>
          <div class="kpi-decoration"></div>
        </div>
        
        <div class="kpi kpi-productos" :key="informes.total_productos_vendidos">
          <div class="kpi-icon">🛍️</div>
          <div class="kpi-content">
            <h3>Productos Vendidos</h3>
            <p class="kpi-value">{{ informes.total_productos_vendidos }}</p>
          </div>
          <div class="kpi-decoration"></div>
        </div>
        
        <div class="kpi kpi-ingresos" :key="informes.total_ingresos">
          <div class="kpi-icon">💰</div>
          <div class="kpi-content">
            <h3>Total Ingresos</h3>
            <p class="kpi-value">{{ formatoPesos(informes.total_ingresos) }}</p>
          </div>
          <div class="kpi-decoration"></div>
        </div>
      </div>
    </div>

    <!-- Gráficas con diseño mejorado -->
    <div class="charts-section">
      <h2 class="section-title">Análisis Visual</h2>
      <div class="graficas">
        <div class="chart-container chart-pie">
          <div class="chart-header">
            <h3>Distribución de Pedidos</h3>
            <i class="chart-icon">🥧</i>
          </div>
          <div class="chart-content">
            <VueApexCharts type="pie" :options="pieOptions" :series="pieSeries" height="350" />
          </div>
        </div>
        
        <div class="chart-container chart-line">
          <div class="chart-header">
            <h3>Tendencia de Ingresos</h3>
            <i class="chart-icon">📈</i>
          </div>
          <div class="chart-content">
            <VueApexCharts type="line" :options="lineOptions" :series="lineSeries" height="350" />
          </div>
        </div>
        
        <div class="chart-container chart-bar">
          <div class="chart-header">
            <h3>Ingresos Mensuales</h3>
            <i class="chart-icon">📊</i>
          </div>
          <div class="chart-content">
            <VueApexCharts type="bar" :options="barOptions" :series="barSeries" height="350" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';

const informes = ref({
  total_pedidos: 0,
  total_pendientes: 0,
  total_productos_vendidos: 0,
  total_ingresos: 0,
  ventas_por_dia: [],
  ventas_por_mes: []
});

const fechaInicio = ref('');
const fechaFin = ref('');

const pieSeries = ref([]);
const lineSeries = ref([]);
const barSeries = ref([]);

const pieOptions = {
  labels: ['Pedidos Aceptados', 'Pedidos Pendientes'],
  colors: ['#4F46E5', '#F59E0B'],
  responsive: [{ breakpoint: 480, options: { legend: { position: 'bottom' } } }],
  legend: { position: 'bottom' },
  plotOptions: {
    pie: {
      donut: {
        size: '65%'
      }
    }
  }
};

const lineOptions = {
  chart: { 
    id: 'lineVentas',
    toolbar: { show: false },
    background: 'transparent'
  },
  colors: ['#10B981'],
  stroke: { width: 3, curve: 'smooth' },
  xaxis: { categories: [] },
  title: { text: 'Ingresos últimos 30 días', style: { fontSize: '16px', fontWeight: 600 } },
  grid: { borderColor: '#E5E7EB' }
};

const barOptions = {
  chart: { 
    id: 'barMes',
    toolbar: { show: false },
    background: 'transparent'
  },
  colors: ['#8B5CF6'],
  xaxis: { categories: [] },
  title: { text: 'Ingresos por Mes (Año actual)', style: { fontSize: '16px', fontWeight: 600 } },
  grid: { borderColor: '#E5E7EB' },
  plotOptions: {
    bar: {
      borderRadius: 8,
      columnWidth: '60%'
    }
  }
};

// 🔍 Función para formatear valores en pesos colombianos
const formatoPesos = (valor) => {
  return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(valor);
};

const obtenerInformes = async () => {
  const token = localStorage.getItem('token');
  const params = {};
  if (fechaInicio.value && fechaFin.value) {
    if (new Date(fechaInicio.value) > new Date(fechaFin.value)) {
      alert('La fecha de inicio no puede ser mayor a la fecha fin.');
      return;
    }
    params.fecha_inicio = fechaInicio.value;
    params.fecha_fin = fechaFin.value;
  }

  try {
    const res = await axios.get('http://localhost:8000/api/gestor/informes', {
      headers: { Authorization: `Bearer ${token}` },
      params: params
    });

    informes.value = res.data;

    pieSeries.value = [
      res.data.total_pedidos,
      res.data.total_pendientes
    ];

    const ventasPorDia = res.data.ventas_por_dia;
    lineOptions.xaxis.categories = ventasPorDia.map(d => d.fecha);
    lineSeries.value = [{
      name: 'Ingresos',
      data: ventasPorDia.map(d => parseFloat(d.total))
    }];

    const ventasPorMes = res.data.ventas_por_mes;
    barOptions.xaxis.categories = ventasPorMes.map(m => 'Mes ' + m.mes);
    barSeries.value = [{
      name: 'Ingresos',
      data: ventasPorMes.map(m => parseFloat(m.total))
    }];

  } catch (err) {
    console.error('Error obteniendo informes:', err);
    alert('Error al obtener los informes. Por favor intente nuevamente.');
  }
};

onMounted(() => obtenerInformes());
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.informes {
  padding: 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header Section */
.header-section {
  text-align: center;
  margin-bottom: 3rem;
  animation: fadeInDown 0.8s ease-out;
}

.titulo {
  font-size: 3rem;
  font-weight: 800;
  color: white;
  margin: 0;
  text-shadow: 0 4px 8px rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.icon {
  font-size: 3rem;
  animation: bounce 2s infinite;
}

.subtitulo {
  color: rgba(255,255,255,0.9);
  font-size: 1.2rem;
  margin-top: 0.5rem;
  font-weight: 300;
}

/* Filtros Section */
.filtros-container {
  display: flex;
  justify-content: center;
  margin-bottom: 3rem;
  animation: fadeInUp 0.8s ease-out 0.2s both;
}

.filtros-card {
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  border: 1px solid rgba(255,255,255,0.2);
}

.filtros-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  font-weight: 600;
  color: #374151;
  font-size: 1.1rem;
}

.filter-icon {
  font-size: 1.2rem;
}

.filtros {
  display: flex;
  gap: 1.5rem;
  align-items: end;
  flex-wrap: wrap;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input-group label {
  font-weight: 500;
  color: #374151;
  font-size: 0.9rem;
}

.date-input {
  padding: 0.75rem 1rem;
  border: 2px solid #E5E7EB;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

.date-input:focus {
  outline: none;
  border-color: #4F46E5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.filter-btn {
  background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.filter-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
}

.btn-icon {
  font-size: 1rem;
}

/* Sections */
.kpis-section, .charts-section {
  margin-bottom: 3rem;
}

.section-title {
  color: white;
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  text-align: center;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

/* KPIs */
.kpis {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  animation: fadeInUp 0.8s ease-out 0.4s both;
}

.kpi {
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 2rem;
  position: relative;
  overflow: hidden;
  transition: all 0.4s ease;
  cursor: pointer;
  border: 1px solid rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.kpi:hover {
  transform: translateY(-8px);
  box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.kpi-icon {
  font-size: 3rem;
  opacity: 0.8;
  animation: pulse 2s infinite;
}

.kpi-content {
  flex: 1;
}

.kpi-content h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.kpi-value {
  margin: 0;
  font-size: 2rem;
  font-weight: 800;
  color: #1F2937;
  animation: countUp 0.8s ease-out;
}

.kpi-decoration {
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  opacity: 0.1;
  transition: all 0.4s ease;
}

.kpi-pedidos .kpi-decoration {
  background: #4F46E5;
}

.kpi-pendientes .kpi-decoration {
  background: #F59E0B;
}

.kpi-productos .kpi-decoration {
  background: #10B981;
}

.kpi-ingresos .kpi-decoration {
  background: #EF4444;
}

.kpi:hover .kpi-decoration {
  transform: scale(1.2);
  opacity: 0.2;
}

/* Charts */
.graficas {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;
  animation: fadeInUp 0.8s ease-out 0.6s both;
}

.chart-container {
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  transition: all 0.4s ease;
}

.chart-container:hover {
  transform: translateY(-5px);
  box-shadow: 0 30px 60px rgba(0,0,0,0.15);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #F3F4F6;
}

.chart-header h3 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #1F2937;
}

.chart-icon {
  font-size: 1.5rem;
  opacity: 0.7;
}

.chart-content {
  animation: fadeIn 1s ease-out 0.8s both;
}

/* Animations */
@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-10px);
  }
  60% {
    transform: translateY(-5px);
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes countUp {
  from {
    transform: scale(0.8);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .informes {
    padding: 1rem;
  }
  
  .titulo {
    font-size: 2rem;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .filtros {
    flex-direction: column;
    align-items: center;
  }
  
  .kpis {
    grid-template-columns: 1fr;
  }
  
  .graficas {
    grid-template-columns: 1fr;
  }
  
  .chart-container {
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  .filtros-card {
    padding: 1rem;
  }
  
  .kpi {
    padding: 1.5rem;
    flex-direction: column;
    text-align: center;
  }
  
  .kpi-icon {
    font-size: 2.5rem;
  }
  
  .kpi-value {
    font-size: 1.5rem;
  }
}
</style>