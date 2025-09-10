<template>
  <div class="register">
    <h2>Registro</h2>
    <form @submit.prevent="register">
      <input v-model="name" type="text" placeholder="Nombre" required />
      <input v-model="email" type="email" placeholder="Correo" required />
      <input v-model="password" type="password" placeholder="Contraseña" required />
      <input v-model="password_confirmation" type="password" placeholder="Confirmar contraseña" required />
      <button type="submit">Registrarse</button>
    </form>
    <p v-if="error" style="color:red">{{ error }}</p>
    <p v-if="success" style="color:green">{{ success }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import http from '../shared/api/http' // 👈 usa tu instancia con baseURL (ajusta la ruta si no usas alias '@')

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')
const success = ref('')

const register = async () => {
  error.value = ''
  success.value = ''
  try {
    await http.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    })
    success.value = 'Usuario registrado correctamente. Ahora puedes iniciar sesión.'
    name.value = ''
    email.value = ''
    password.value = ''
    password_confirmation.value = ''
  } catch (err: any) {
    console.error(err)
    if (err.response?.data?.errors) {
      error.value = (Object.values(err.response.data.errors) as string[][]).flat().join(' ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = err.message || 'Error de red'
    }
  }
}
</script>
