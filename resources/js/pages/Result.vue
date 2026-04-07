<template>
  <div class="result-page">
    <div class="result-card">
      <div class="result-header">
        <span class="result-badge">Résultat final</span>
        <h1>Ton résultat politique</h1>
        <p>
          Voici le classement des partis en fonction de tes réponses dans cette version MVP de PolitiScan.
        </p>
      </div>

      <div v-if="loading" class="result-info">
        Calcul en cours...
      </div>

      <div v-else-if="results.length">
        <div class="winner-box">
          <span>Tu es le plus proche de</span>
          <h2>{{ topParty }}</h2>
        </div>

        <div class="chart-box">
          <canvas ref="chartCanvas"></canvas>
        </div>

        <div class="results-list">
          <div v-for="(result, index) in results" :key="index" class="result-item">
            <div class="party-info">
              <strong>{{ result.party.name }}</strong>
              <small>{{ result.party.short_name }}</small>
            </div>
            <div class="score-badge">
              {{ Number(result.score).toFixed(1) }}%
            </div>
          </div>
        </div>
      </div>

      <div v-else class="result-info error">
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
    const sessionId = this.state.sessionId || localStorage.getItem('sessionId')

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
              backgroundColor: ['#2563eb', '#60a5fa', '#34d399', '#f59e0b'],
              borderRadius: 10,
              borderSkipped: false
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              labels: {
                color: '#334155'
              }
            }
          },
          scales: {
            x: {
              ticks: {
                color: '#475569'
              },
              grid: {
                display: false
              }
            },
            y: {
              beginAtZero: true,
              max: 100,
              ticks: {
                color: '#475569',
                callback: (value) => `${value}%`
              },
              grid: {
                color: '#e2e8f0'
              }
            }
          }
        }
      })
    }
  }
}
</script>

<style scoped>
.result-page {
  min-height: 100vh;
  padding: 40px 20px;
  background: linear-gradient(180deg, #f7fbff 0%, #edf4ff 100%);
  font-family: Arial, sans-serif;
}

.result-card {
  max-width: 860px;
  margin: 0 auto;
  background: #ffffff;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 14px 40px rgba(27, 61, 122, 0.12);
  border: 1px solid #d9e5f6;
}

.result-header {
  margin-bottom: 24px;
}

.result-badge {
  display: inline-block;
  background: #e8f0ff;
  color: #2457c5;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 14px;
}

.result-header h1 {
  margin: 0 0 10px;
  font-size: 32px;
  color: #163456;
}

.result-header p {
  margin: 0;
  line-height: 1.6;
  color: #5e7087;
}

.winner-box {
  text-align: center;
  background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%);
  border: 1px solid #dbe7fb;
  border-radius: 18px;
  padding: 24px;
  margin-bottom: 24px;
}

.winner-box span {
  display: block;
  color: #5e7087;
  margin-bottom: 8px;
  font-weight: 600;
}

.winner-box h2 {
  margin: 0;
  font-size: 34px;
  color: #2563eb;
}

.chart-box {
  background: #ffffff;
  border: 1px solid #d9e5f6;
  border-radius: 18px;
  padding: 18px;
  margin-bottom: 24px;
}

.results-list {
  display: grid;
  gap: 12px;
}

.result-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 14px;
  padding: 16px 18px;
  border-radius: 14px;
  border: 1px solid #dbe5f3;
  background: #f9fbff;
}

.party-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.party-info strong {
  color: #17324d;
  font-size: 16px;
}

.party-info small {
  color: #6b7d93;
  font-size: 13px;
}

.score-badge {
  background: #2563eb;
  color: #ffffff;
  border-radius: 999px;
  padding: 8px 14px;
  font-weight: 700;
  font-size: 14px;
  min-width: 78px;
  text-align: center;
}

.result-info {
  padding: 18px;
  border-radius: 14px;
  background: #f8fbff;
  color: #4f647f;
  border: 1px solid #d9e5f6;
}

.result-info.error {
  background: #fff1f2;
  color: #b42318;
  border-color: #fecdd3;
}

@media (max-width: 640px) {
  .result-card {
    padding: 22px;
  }

  .result-header h1 {
    font-size: 28px;
  }

  .winner-box h2 {
    font-size: 28px;
  }

  .result-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .score-badge {
    align-self: flex-start;
  }
}
</style>
