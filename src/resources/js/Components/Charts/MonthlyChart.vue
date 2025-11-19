<script setup>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'

// Registrasi komponen Chart.js
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const props = defineProps({
    data: Array // Menerima array angka [5, 10, 0, ...]
})

const chartData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'],
  datasets: [
    {
      label: 'Total Booking',
      backgroundColor: (ctx) => {
        const canvas = ctx.chart.ctx;
        const gradient = canvas.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // Warna Indigo pudar
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');
        return gradient;
      },
      borderColor: '#4F46E5', // Indigo-600
      pointBackgroundColor: '#fff',
      pointBorderColor: '#4F46E5',
      pointHoverBackgroundColor: '#4F46E5',
      pointHoverBorderColor: '#fff',
      borderWidth: 2,
      fill: true,
      tension: 0.4, // Membuat garis melengkung halus
      data: props.data
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false }, // Sembunyikan legend agar bersih
    tooltip: {
        backgroundColor: '#1F2937',
        padding: 12,
        cornerRadius: 8,
    }
  },
  scales: {
    y: {
        beginAtZero: true,
        grid: { borderDash: [4, 4], color: '#F3F4F6' },
        ticks: { stepSize: 1 }
    },
    x: {
        grid: { display: false }
    }
  }
}
</script>

<template>
  <div class="h-[300px] w-full">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>