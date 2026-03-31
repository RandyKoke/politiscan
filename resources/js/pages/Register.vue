<template>
  <div>
    <h1>Créer un compte</h1>

    <input v-model="name" placeholder="Nom">
    <input v-model="email" placeholder="Email">
    <input v-model="password" placeholder="Mot de passe">

    <button @click="register">S'inscrire</button>

    <p>{{ message }}</p>

    <button @click="goToLogin">Déjà un compte ? Se connecter</button>
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
      message: ''
    }
  },
  methods: {
    async register() {
      try {
        const res = await fetch('http://127.0.0.1:8000/api/register', {
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
          this.message = "Compte créé avec succès"
          this.state.page = 'login'
        } else {
          this.message = data.message
        }

      } catch (err) {
        this.message = "Erreur serveur"
      }
    },
    goToLogin() {
      this.state.page = 'login'
    }
  }
}
</script>
