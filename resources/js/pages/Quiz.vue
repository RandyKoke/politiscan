<template>
  <div class="quiz-page">
    <div class="quiz-card">
      <div class="quiz-header">
        <span class="quiz-badge">PolitiScan</span>
        <h1>Questionnaire</h1>
        <p>
          Réponds selon ton degré d’accord. Tes réponses seront comparées avec les positions des partis
          intégrés dans ce MVP.
        </p>
      </div>

      <div v-if="loading" class="quiz-info">
        Chargement du questionnaire...
      </div>

      <div v-else-if="!questions.length" class="quiz-info error">
        Aucune question disponible.
      </div>

      <div v-else>
        <div class="progress-top">
          <span>Question {{ currentIndex + 1 }} sur {{ questions.length }}</span>
          <span>{{ progressPercent }}%</span>
        </div>

        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: progressPercent + '%' }"></div>
        </div>

        <div class="question-box">
          <h2>{{ currentQuestion.text }}</h2>
        </div>

        <div class="answers">
          <button class="answer-btn strong-no" @click="answer(-2)">Pas du tout d'accord</button>
          <button class="answer-btn no" @click="answer(-1)">Pas d'accord</button>
          <button class="answer-btn neutral" @click="answer(0)">Neutre</button>
          <button class="answer-btn yes" @click="answer(1)">D'accord</button>
          <button class="answer-btn strong-yes" @click="answer(2)">Tout à fait d'accord</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { inject } from 'vue'

export default {
  setup() {
    const state = inject('state')
    return { state }
  },

  data() {
    return {
      questions: [],
      currentIndex: 0,
      sessionId: null,
      loading: true
    }
  },

  computed: {
    currentQuestion() {
      return this.questions[this.currentIndex] || null
    },

    progressPercent() {
      if (!this.questions.length) return 0
      return Math.round(((this.currentIndex + 1) / this.questions.length) * 100)
    }
  },

  async mounted() {
    if (!this.state.user || !this.state.user.id) {
      alert('Utilisateur non connecté.')
      this.state.page = 'login'
      return
    }

    const questionsLoaded = await this.fetchQuestions()

    if (!questionsLoaded) {
      this.loading = false
      return
    }

    const sessionStarted = await this.startSession()

    if (!sessionStarted) {
      this.loading = false
      return
    }

    this.loading = false
  },

  methods: {
    async fetchQuestions() {
      try {
        const res = await fetch('/api/questions')

        if (!res.ok) {
          throw new Error('Erreur HTTP lors du chargement des questions')
        }

        const data = await res.json()
        this.questions = Array.isArray(data) ? data : []

        if (!this.questions.length) {
          return false
        }

        return true
      } catch (error) {
        console.error('Erreur chargement questions:', error)
        alert('Impossible de charger les questions.')
        return false
      }
    },

    async startSession() {
      try {
        const res = await fetch('/api/quiz/start', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            user_id: this.state.user.id
          })
        })

        if (!res.ok) {
          throw new Error('Erreur HTTP au démarrage de la session')
        }

        const data = await res.json()

        this.sessionId = data.id
        this.state.sessionId = data.id
        localStorage.setItem('sessionId', data.id)

        return true
      } catch (error) {
        console.error('Erreur session:', error)
        alert('Impossible de démarrer le quiz.')
        return false
      }
    },

    async answer(value) {
      if (!this.currentQuestion || !this.sessionId) {
        alert('Session ou question invalide.')
        return
      }

      try {
        const res = await fetch('/api/quiz/answer', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            session_id: this.sessionId,
            question_id: this.currentQuestion.id,
            answer_value: value
          })
        })

        if (!res.ok) {
          throw new Error('Erreur HTTP lors de l’enregistrement de la réponse')
        }

        if (this.currentIndex < this.questions.length - 1) {
          this.currentIndex++
        } else {
          this.state.page = 'result'
        }
      } catch (error) {
        console.error('Erreur réponse:', error)
        alert('Erreur lors de l’enregistrement.')
      }
    }
  }
}
</script>

<style scoped>
.quiz-page {
  min-height: 100vh;
  padding: 40px 20px;
  background: linear-gradient(180deg, #f4f9ff 0%, #eaf2ff 100%);
  font-family: Arial, sans-serif;
}

.quiz-card {
  max-width: 760px;
  margin: 0 auto;
  background: #ffffff;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 14px 40px rgba(27, 61, 122, 0.12);
  border: 1px solid #d9e5f6;
}

.quiz-header {
  margin-bottom: 28px;
}

.quiz-badge {
  display: inline-block;
  background: #e8f0ff;
  color: #2457c5;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 14px;
}

.quiz-header h1 {
  margin: 0 0 10px;
  font-size: 32px;
  color: #163456;
}

.quiz-header p {
  margin: 0;
  line-height: 1.6;
  color: #5e7087;
}

.progress-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  font-size: 15px;
  font-weight: 700;
  color: #33537b;
}

.progress-bar {
  width: 100%;
  height: 12px;
  background: #e6eefb;
  border-radius: 999px;
  overflow: hidden;
  margin-bottom: 26px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #2f6fed 0%, #5ca2ff 100%);
  border-radius: 999px;
  transition: width 0.3s ease;
}

.question-box {
  background: #f8fbff;
  border: 1px solid #d9e5f6;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
}

.question-box h2 {
  margin: 0;
  font-size: 26px;
  line-height: 1.45;
  color: #17324d;
}

.answers {
  display: grid;
  gap: 12px;
}

.answer-btn {
  width: 100%;
  text-align: left;
  padding: 15px 18px;
  border-radius: 14px;
  border: none;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: transform 0.15s ease, opacity 0.2s ease, box-shadow 0.2s ease;
}

.answer-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 18px rgba(27, 61, 122, 0.12);
}

.strong-no {
  background: #fff1f2;
  color: #a4324a;
}

.no {
  background: #fff7ed;
  color: #b45309;
}

.neutral {
  background: #f3f4f6;
  color: #374151;
}

.yes {
  background: #eff6ff;
  color: #1d4ed8;
}

.strong-yes {
  background: #ecfdf3;
  color: #15803d;
}

.quiz-info {
  padding: 18px;
  border-radius: 14px;
  background: #f8fbff;
  color: #4f647f;
  border: 1px solid #d9e5f6;
}

.quiz-info.error {
  background: #fff1f2;
  color: #b42318;
  border-color: #fecdd3;
}

@media (max-width: 640px) {
  .quiz-card {
    padding: 22px;
  }

  .quiz-header h1 {
    font-size: 28px;
  }

  .question-box h2 {
    font-size: 22px;
  }

  .progress-top {
    font-size: 14px;
  }
}
</style>
