<template>
  <div class="container">
    <h1 class="title">Ton résultat politique</h1>

    <div v-if="loading">Calcul en cours...</div>

    <div v-else>
      <div v-if="results.length">
        <div class="card">
          <h2>Tu es proche de</h2>
          <h1 class="winner">{{ topParty }}</h1>
        </div>

        <canvas ref="chartCanvas"></canvas>

        <div class="list">
          <div v-for="(result, index) in results" :key="index" class="item">
            <span>{{ result.party.name }}</span>
            <span>{{ Number(result.score).toFixed(1) }}%</span>
          </div>
        </div>
      </div>

      <div v-else>
        <p>Aucun résultat disponible.</p>
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
      loading: true
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

  methods: {
    renderChart() {
      if (!this.$refs.chartCanvas || !this.results.length) return

      const labels = this.results.map(r => r.party.short_name)
      const data = this.results.map(r => Number(r.score))

      const ctx = this.$refs.chartCanvas.getContext('2d')

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {
              label: 'Compatibilité (%)',
              data
            }
          ]
        }
      })
    }
  }
}
</script>

<style>
.container {
  max-width: 600px;
  margin: auto;
  text-align: center;
  font-family: Arial;
}

.title {
  margin-bottom: 20px;
}

.card {
  background: #f3f4f6;
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 20px;
}

.winner {
  color: #2563eb;
}

.list {
  margin-top: 20px;
}

.item {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}
</style>
