<template>
  <div>
    <h1>Questionnaire</h1>

    <div v-if="loading">Chargement...</div>

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
      return this.questions[this.currentIndex]
    }
  },

  async mounted() {
    await this.fetchQuestions()
    await this.startSession()
    this.loading = false
  },

  methods: {
    async fetchQuestions() {
      try {
        const res = await fetch('http://127.0.0.1:8000/api/questions')
        this.questions = await res.json()
      } catch (error) {
        console.error('Erreur chargement questions:', error)
        alert('Impossible de charger les questions.')
      }
    },

    async startSession() {
      try {
        const res = await fetch('http://127.0.0.1:8000/api/quiz/start', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: 1 })
        })

        const data = await res.json()

        this.sessionId = data.id

        // 🔥 persistance
        localStorage.setItem('sessionId', data.id)
        this.state.sessionId = data.id

      } catch (error) {
        console.error('Erreur session:', error)
        alert('Impossible de démarrer le quiz.')
      }
    },

    async answer(value) {
      try {
        await fetch('http://127.0.0.1:8000/api/quiz/answer', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            session_id: this.sessionId,
            question_id: this.currentQuestion.id,
            answer_value: value
          })
        })

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
