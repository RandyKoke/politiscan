<template>
  <div class="quiz-layout">
    <div class="quiz-card">
      <span class="hero-badge">Questionnaire</span>
      <h1 class="question-title">Exprime ton positionnement</h1>

      <div v-if="loading" class="helper-box">
        Chargement du questionnaire...
      </div>

      <div v-else-if="!questions.length" class="helper-box">
        Aucune question disponible.
      </div>

      <div v-else>
        <div class="progress-meta">
          <span>Question {{ currentIndex + 1 }} sur {{ questions.length }}</span>
          <span>{{ progressPercent }}%</span>
        </div>

        <div class="progress-bar">
          <div
            class="progress-bar-fill"
            :style="{ width: progressPercent + '%' }"
          ></div>
        </div>

        <p class="page-subtitle">
          Réponds selon ton degré d'accord. Le résultat final compare tes réponses avec les positions
          des partis intégrés dans ce MVP.
        </p>

        <p class="question-text">{{ currentQuestion.text }}</p>

        <div class="answer-grid">
          <button class="btn btn-answer" @click="answer(-2)">
            Pas du tout d'accord
          </button>
          <button class="btn btn-answer" @click="answer(-1)">
            Pas d'accord
          </button>
          <button class="btn btn-answer" @click="answer(0)">
            Neutre
          </button>
          <button class="btn btn-answer" @click="answer(1)">
            D'accord
          </button>
          <button class="btn btn-answer" @click="answer(2)">
            Tout à fait d'accord
          </button>
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
          alert('Aucune question trouvée.')
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
