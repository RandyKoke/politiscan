<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>PolitiScan</title>
    @vite('resources/js/app.js')
</head>
<body>
    <div id="app">

        <!-- On change manuellement la page -->
        <register-page v-if="state.page === 'register'"></register-page>
        <login-page v-if="state.page === 'login'"></login-page>
        <quiz-page v-if="state.page === 'quiz'"></quiz-page>
        <result-page v-if="state.page === 'result'"></result-page>

    </div>

    <script>
        window.appState = {
            page: 'register',
            sessionId: null
        }
    </script>
</body>
</html>




