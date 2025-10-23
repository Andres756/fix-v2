// src/main.ts
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { getToken } from './auth/auth'
import Vue3Toastify, { type ToastContainerOptions } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import './style.css'

const token = getToken()
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

const app = createApp(App)

app.use(Vue3Toastify, {
  autoClose: 3000,
  position: 'top-right',
  style: {
    zIndex: 99999, // ✅ Más alto que el modal (9999)
  },
} as ToastContainerOptions)


app.use(router)
app.mount('#app')
