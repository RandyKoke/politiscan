import { createApp, reactive, watch } from 'vue'

import Login from './pages/Login.vue'
import Register from './pages/Register.vue'
import Quiz from './pages/Quiz.vue'
import Result from './pages/Result.vue'

const state = reactive({
    page: 'login',
    user: null,
    sessionId: localStorage.getItem('sessionId') || null
})

watch(
    () => state.sessionId,
    (value) => {
        if (value) {
            localStorage.setItem('sessionId', value)
        } else {
            localStorage.removeItem('sessionId')
        }
    }
)

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
