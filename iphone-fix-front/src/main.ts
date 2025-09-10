// src/main.ts
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { getToken } from './auth/auth'
import 'vue3-toastify/dist/index.css'
import './style.css'

const token = getToken()
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

const app = createApp(App)
app.use(router)
app.mount('#app')
