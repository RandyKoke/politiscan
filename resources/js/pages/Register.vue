<template>
  <div class="page-shell">
    <div class="card-shell">
      <span class="hero-badge">Créer un compte</span>
      <h1 class="page-title">Rejoins PolitiScan</h1>
      <p class="page-subtitle">
        Crée ton compte pour lancer le questionnaire et consulter ton résultat politique.
      </p>

      <div class="form-group">
        <label class="input-label" for="name">Nom</label>
        <input
          id="name"
          v-model="name"
          class="text-input"
          type="text"
          placeholder="Ton nom"
          autocomplete="name"
        />
      </div>

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
          placeholder="Minimum 6 caractères"
          autocomplete="new-password"
        />
      </div>

      <button class="btn btn-primary" @click="register">
        S'inscrire
      </button>

      <div class="link-row">
        <button class="btn btn-secondary" @click="goToLogin">
          Déjà un compte ? Se connecter
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
      name: '',
      email: '',
      password: '',
      message: '',
      messageType: 'error'
    }
  },

  methods: {
    async register() {
      this.message = ''

      try {
        const res = await fetch('/api/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            name: this.name,
            email: this.email,
            password: this.password
          })
        })

        const data = await res.json()

        if (res.ok) {
          this.message = 'Compte créé avec succès'
          this.messageType = 'success'
          this.state.page = 'login'
        } else {
          this.message = data.message || 'Impossible de créer le compte'
          this.messageType = 'error'
        }
      } catch (err) {
        this.message = 'Erreur serveur'
        this.messageType = 'error'
      }
    },

    goToLogin() {
      this.message = ''
      this.state.page = 'login'
    }
  }
}
</script>
