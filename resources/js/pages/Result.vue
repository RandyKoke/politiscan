<template>
  <div class="result-layout">
    <div class="result-card">
      <span class="hero-badge">Résultats</span>
      <h1 class="page-title">Ton résultat politique</h1>
      <p class="page-subtitle">
        Voici le parti qui se rapproche le plus de tes réponses dans cette version MVP de PolitiScan.
      </p>

      <div v-if="loading" class="helper-box">
        Calcul en cours...
      </div>

      <div v-else-if="results.length">
        <div class="result-hero">
          <h2>Tu es le plus proche de</h2>
          <p class="winner">{{ topParty }}</p>
        </div>

        <div class="chart-wrap">
          <canvas ref="chartCanvas"></canvas>
        </div>

        <div class="result-list">
          <div v-for="(result, index) in results" :key="index" class="result-item">
            <strong>{{ result.party.name }}</strong>
            <span>{{ Number(result.score).toFixed(1) }}%</span>
          </div>
        </div>
      </div>

      <div v-else class="helper-box">
        Aucun résultat disponible.
      </div>
    </div>
  </div>
</template>

<script>
import { inject } from 'vue'
import Chart from 'chart.js/auto'

export default {
  setup() {
    const state = inject('state')
    return { state }
  },

  data() {
    return {
      results: [],
      loading: true,
      chartInstance: null
    }
  },

  computed: {
    topParty() {
      return this.results.length ? this.results[0].party.name : ''
    }
  },

  async mounted() {
    const sessionId = this.state.sessionId

    if (!sessionId) {
      alert('Session perdue. Relance le quiz.')
      this.state.page = 'quiz'
      return
    }

    try {
      const res = await fetch(`/api/results/${sessionId}`)

      if (!res.ok) {
        throw new Error('Erreur HTTP lors du calcul des résultats')
      }

      const data = await res.json()

      this.results = Array.isArray(data) ? data : []
      this.loading = false

      if (this.results.length) {
        this.$nextTick(() => {
          this.renderChart()
        })
      }
    } catch (error) {
      console.error(error)
      alert('Erreur lors du calcul des résultats')
      this.loading = false
    }
  },

  beforeUnmount() {
    if (this.chartInstance) {
      this.chartInstance.destroy()
      this.chartInstance = null
    }
  },

  methods: {
    renderChart() {
      if (!this.$refs.chartCanvas || !this.results.length) return

      if (this.chartInstance) {
        this.chartInstance.destroy()
      }

      const labels = this.results.map((r) => r.party.short_name)
      const data = this.results.map((r) => Number(r.score))
      const ctx = this.$refs.chartCanvas.getContext('2d')

      this.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'Compatibilité (%)',
              data,
              backgroundColor: ['#2f6fed', '#5b8def', '#8ab0f5', '#bfd4fb'],
              borderRadius: 10,
              borderSkipped: false
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            y: {
              beginAtZero: true,
              max: 100,
              ticks: {
                callback: (value) => `${value}%`
              }
            }
          },
          plugins: {
            legend: {
              display: true
            }
          }
        }
      })
    }
  }
}
</script>
