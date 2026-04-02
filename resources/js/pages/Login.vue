<template>
  <div>
    <h1>Connexion</h1>

    <input v-model="email" placeholder="Email">
    <input v-model="password" type="password" placeholder="Mot de passe">

    <button @click="login">Se connecter</button>

    <p>{{ message }}</p>

    <button @click="goToRegister">Créer un compte</button>
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
      message: ''
    }
  },
  methods: {
    async login() {
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
          this.message = "Connexion réussie"
          console.log(data)

          this.state.page = 'quiz'
        } else {
          this.message = data.message
        }

      } catch (err) {
        this.message = "Erreur serveur"
      }
    },
    goToRegister() {
      this.state.page = 'register'
    }
  }
}
</script>
