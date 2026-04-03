<template>
  <div>
    <h1>Questionnaire</h1>

    <div v-if="loading">Chargement...</div>

    <div v-else-if="!questions.length">
      <p>Aucune question disponible.</p>
    </div>

    <div v-else>
      <p><strong>Question {{ currentIndex + 1 }} / {{ questions.length }}</strong></p>

      <h2>{{ currentQuestion.text }}</h2>

      <div style="margin-top:20px;">
        <button @click="answer(-2)">Pas du tout d'accord</button>
        <button @click="answer(-1)">Pas d'accord</button>
        <button @click="answer(0)">Neutre</button>
        <button @click="answer(1)">D'accord</button>
        <button @click="answer(2)">Tout à fait d'accord</button>
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
