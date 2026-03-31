import { createApp, reactive } from 'vue'

import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import Quiz from './pages/Quiz.vue'
import Result from './pages/Result.vue'

// state global avec persistance
const state = reactive({
    page: 'login',
    sessionId: localStorage.getItem('sessionId') || null
})

const app = createApp({
    setup() {
        return { state }
    },
    provide: {
        state
    }
})

app.component('login-page', Login)
app.component('register-page', Register)
app.component('quiz-page', Quiz)
app.component('result-page', Result)

app.mount('#app')
