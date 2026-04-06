<template>
  <div class="page-shell">
    <div class="card-shell">
      <span class="hero-badge">PolitiScan - version MVP de mon TFE</span>
      <h1 class="page-title">Bienvenue sur PolitiScan</h1>
      <p class="page-subtitle">
        Découvre le parti politique francophone le plus proche de tes idées en répondant à un questionnaire simple,
        rapide et interactif.
      </p>

      <div class="form-group">
        <label class="input-label" for="email">Adresse email</label>
        <input
          id="email"
          v-model="email"
          class="text-input"
          type="email"
          placeholder="tonemail@example.com"
          autocomplete="email"
        />
      </div>

      <div class="form-group">
        <label class="input-label" for="password">Mot de passe</label>
        <input
          id="password"
          v-model="password"
          class="text-input"
          type="password"
          placeholder="Ton mot de passe"
          autocomplete="current-password"
        />
      </div>

      <button class="btn btn-primary" @click="login">
        Se connecter
      </button>

      <div class="link-row">
        <button class="btn btn-secondary" @click="goToRegister">
          Créer un compte
        </button>
      </div>

      <p
        v-if="message"
        class="message"
        :class="messageType === 'success' ? 'message-success' : 'message-error'"
      >
        {{ message }}
      </p>
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
      email: '',
      password: '',
      message: '',
      messageType: 'error'
    }
  },

  methods: {
    async login() {
      this.message = ''

      try {
        const res = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            email: this.email,
            password: this.password
          })
        })

        const data = await res.json()

        if (res.ok) {
          this.message = 'Connexion réussie'
          this.messageType = 'success'
          this.state.user = data
          this.state.page = 'quiz'
        } else {
          this.message = data.message || 'Identifiants incorrects'
          this.messageType = 'error'
        }
      } catch (err) {
        this.message = 'Erreur serveur'
        this.messageType = 'error'
      }
    },

    goToRegister() {
      this.message = ''
      this.state.page = 'register'
    }
  }
}
</script>
