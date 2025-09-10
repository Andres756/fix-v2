<template>
  <div class="login-container">
    <!-- Background with Apple-style gradient -->
    <div class="background-gradient"></div>
    
    <!-- Floating elements for visual appeal -->
    <div class="floating-elements">
      <div class="floating-circle circle-1"></div>
      <div class="floating-circle circle-2"></div>
      <div class="floating-circle circle-3"></div>
    </div>

    <div class="login-card">
      <!-- Apple logo and branding -->
      <div class="brand-section">
        <div class="apple-logo">
          <svg width="40" height="48" viewBox="0 0 40 48" fill="none">
            <path d="M30.3 25.5c-.1-4.8 3.9-7.1 4.1-7.2-2.2-3.3-5.7-3.7-6.9-3.8-2.9-.3-5.7 1.7-7.2 1.7-1.5 0-3.8-1.7-6.3-1.6-3.2 0-6.2 1.9-7.9 4.8-3.4 5.9-.9 14.6 2.4 19.4 1.6 2.4 3.6 5 6.1 4.9 2.4-.1 3.4-1.6 6.3-1.6 2.9 0 3.8 1.6 6.3 1.5 2.6 0 4.3-2.3 5.9-4.7 1.9-2.8 2.7-5.5 2.7-5.7-.1 0-5.2-2-5.3-7.9z" fill="currentColor"/>
            <path d="M25.1 8.6c1.4-1.7 2.3-4 2.1-6.3-2 .1-4.5 1.4-5.9 3.1-1.3 1.5-2.4 3.9-2.1 6.2 2.2.2 4.5-1.1 5.9-3z" fill="currentColor"/>
          </svg>
        </div>
        <h1 class="brand-title">Iphone FIX</h1>
        <p class="brand-subtitle">Inicia sesión para continuar</p>
      </div>

      <!-- Login form -->
      <form @submit.prevent="login" class="login-form">
        <div class="input-group">
          <div class="input-wrapper">
            <input 
              v-model="email" 
              type="email" 
              placeholder="Correo electrónico"
              class="form-input"
              required 
            />
            <div class="input-focus-line"></div>
          </div>
        </div>

        <div class="input-group">
          <div class="input-wrapper">
            <input 
              v-model="password" 
              type="password" 
              placeholder="Contraseña"
              class="form-input"
              required 
            />
            <div class="input-focus-line"></div>
          </div>
        </div>

        <button type="submit" class="login-button" :disabled="isLoading">
          <span v-if="!isLoading" class="button-content">
            <span class="button-text">Iniciar Sesión</span>
            <svg class="button-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none">
              <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          <span v-else class="loading-spinner"></span>
        </button>

        <!-- Error message -->
        <div v-if="error" class="error-message">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2"/>
            <path d="M8 4v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          {{ error }}
        </div>
      </form>


    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../shared/api/http'              // instancia axios con baseURL (ajusta la ruta si no usas '@')
import { setToken, setUser } from '../auth/auth'  // helpers de auth

const email = ref('')
const password = ref('')
const error = ref('')
const isLoading = ref(false)
const router = useRouter()

const login = async () => {
  error.value = ''
  isLoading.value = true
  try {
    const { data } = await http.post('/login', {
      email: email.value,
      password: password.value,
    })
    // guarda credenciales en storage
    setToken(data.access_token)
    setUser(data.user)

    router.push('/dashboard')
  } catch (err: any) {
    console.error(err)
    error.value =
      err?.response?.data?.message ||
      err?.message ||
      'Error al iniciar sesión.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Reset and base styles */
* {
  box-sizing: border-box;
}

.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Background gradient */
.background-gradient {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #6B73FF 0%, #9644A1 50%, #C73E1D 100%);
  opacity: 0.9;
}

.background-gradient::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 80%, rgba(199, 62, 29, 0.2) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 50% 50%, rgba(150, 68, 161, 0.15) 0%, transparent 70%);
  animation: gradientShift 6s ease-in-out infinite alternate;
}

@keyframes gradientShift {
  0% { opacity: 0.7; }
  100% { opacity: 1; }
}

/* Floating elements */
.floating-elements {
  position: absolute;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.floating-circle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  animation: float 8s ease-in-out infinite;
}

.circle-1 {
  width: 120px;
  height: 120px;
  top: 10%;
  left: 10%;
  animation-delay: 0s;
}

.circle-2 {
  width: 80px;
  height: 80px;
  top: 70%;
  right: 10%;
  animation-delay: -2s;
}

.circle-3 {
  width: 60px;
  height: 60px;
  bottom: 20%;
  left: 20%;
  animation-delay: -4s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  33% { transform: translateY(-20px) rotate(120deg); }
  66% { transform: translateY(10px) rotate(240deg); }
}

/* Login card */
.login-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 24px;
  padding: 48px 40px;
  width: 100%;
  max-width: 420px;
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.05);
  position: relative;
  z-index: 10;
  animation: cardSlideIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
  transition: all 0.3s ease;
}

.login-card:hover {
  transform: translateY(-5px);
  box-shadow: 
    0 35px 60px -12px rgba(0, 0, 0, 0.3),
    0 0 0 1px rgba(255, 255, 255, 0.1);
}

@keyframes cardSlideIn {
  0% {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Brand section */
.brand-section {
  text-align: center;
  margin-bottom: 40px;
}

.apple-logo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #007AFF, #5856D6, #C73E1D);
  border-radius: 16px;
  color: white;
  margin-bottom: 20px;
  animation: logoFloat 3s ease-in-out infinite;
  transition: transform 0.3s ease;
}

.apple-logo:hover {
  transform: scale(1.1) rotate(5deg);
}

@keyframes logoFloat {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

.brand-title {
  font-size: 28px;
  font-weight: 700;
  color: #1d1d1f;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.brand-subtitle {
  color: #86868b;
  font-size: 16px;
  margin: 0;
  font-weight: 400;
}

/* Form styles */
.login-form {
  space-y: 24px;
}

.input-group {
  margin-bottom: 24px;
}

.input-wrapper {
  position: relative;
}

.form-input {
  width: 100%;
  padding: 16px 20px;
  border: 2px solid #e5e5e7;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 400;
  background: rgba(255, 255, 255, 0.8);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  outline: none;
  color: #1d1d1f;
}

.form-input::placeholder {
  color: #86868b;
}

.form-input:focus {
  border-color: #C73E1D;
  background: white;
  transform: translateY(-2px);
  box-shadow: 
    0 10px 25px rgba(199, 62, 29, 0.15),
    0 0 0 3px rgba(199, 62, 29, 0.1);
}

.input-focus-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #007AFF, #5856D6, #C73E1D);
  transition: all 0.3s ease;
  transform: translateX(-50%);
  border-radius: 1px;
}

.form-input:focus + .input-focus-line {
  width: 100%;
}

/* Login button */
.login-button {
  width: 100%;
  padding: 16px 24px;
  background: linear-gradient(135deg, #007AFF 0%, #5856D6 50%, #C73E1D 100%);
  border: none;
  border-radius: 12px;
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  margin: 32px 0 24px 0;
}

.login-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.login-button:hover::before {
  left: 100%;
}

.login-button:hover {
  transform: translateY(-2px);
  box-shadow: 
    0 15px 30px rgba(199, 62, 29, 0.3),
    0 5px 15px rgba(0, 0, 0, 0.1);
}

.login-button:active {
  transform: translateY(0);
}

.login-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.button-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.button-arrow {
  transition: transform 0.3s ease;
}

.login-button:hover .button-arrow {
  transform: translateX(4px);
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Error message */
.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #ff3b30;
  background: rgba(255, 59, 48, 0.1);
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 14px;
  margin-top: 16px;
  border: 1px solid rgba(255, 59, 48, 0.2);
  animation: errorSlideIn 0.3s ease;
}

@keyframes errorSlideIn {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Additional options */


/* Responsive design */
@media (max-width: 768px) {
  .login-container {
    padding: 16px;
  }
  
  .login-card {
    margin: 0;
    padding: 40px 32px;
    max-width: 100%;
    border-radius: 20px;
  }
  
  .brand-title {
    font-size: 26px;
  }
  
  .brand-subtitle {
    font-size: 15px;
  }
  
  .apple-logo {
    width: 56px;
    height: 56px;
  }
}

@media (max-width: 480px) {
  .login-container {
    padding: 12px;
  }
  
  .login-card {
    margin: 0;
    padding: 32px 24px;
    border-radius: 16px;
  }
  
  .brand-title {
    font-size: 24px;
  }
  
  .brand-subtitle {
    font-size: 14px;
  }
  
  .apple-logo {
    width: 52px;
    height: 52px;
    margin-bottom: 16px;
  }
  
  .form-input {
    padding: 14px 16px;
    font-size: 16px; /* Prevent zoom on iOS */
    border-radius: 10px;
  }
  
  .login-button {
    padding: 14px 20px;
    font-size: 15px;
    border-radius: 10px;
  }
  
  .floating-circle {
    display: none; /* Hide floating elements on mobile for better performance */
  }
}

@media (max-width: 360px) {
  .login-card {
    padding: 28px 20px;
  }
  
  .brand-title {
    font-size: 22px;
  }
  
  .apple-logo {
    width: 48px;
    height: 48px;
  }
  
  .form-input {
    padding: 12px 14px;
  }
  
  .login-button {
    padding: 12px 18px;
  }
}

/* Landscape orientation on mobile */
@media (max-height: 600px) and (orientation: landscape) {
  .login-container {
    padding: 8px;
  }
  
  .login-card {
    padding: 24px 32px;
    max-height: 90vh;
    overflow-y: auto;
  }
  
  .brand-section {
    margin-bottom: 24px;
  }
  
  .apple-logo {
    width: 44px;
    height: 44px;
    margin-bottom: 12px;
  }
  
  .brand-title {
    font-size: 20px;
  }
  
  .input-group {
    margin-bottom: 16px;
  }
  
  .floating-circle {
    display: none;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .login-card {
    background: rgba(28, 28, 30, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .brand-title {
    color: #f2f2f7;
  }
  
  .brand-subtitle {
    color: #8e8e93;
  }
  
  .form-input {
    background: rgba(44, 44, 46, 0.8);
    border-color: #48484a;
    color: #f2f2f7;
  }
  
  .form-input::placeholder {
    color: #8e8e93;
  }
  
  .divider span {
    background: rgba(28, 28, 30, 0.95);
  }
}
</style>