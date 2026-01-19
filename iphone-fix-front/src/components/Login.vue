<template>
  <div class="login-container">
    <!-- Background Pattern -->
    <div class="background-pattern"></div>
    
    <div class="login-card">
      <!-- Logo Section -->
      <div class="brand-section">
        <div class="logo-wrapper">
          <!-- 🎨 COLOCA TU LOGO AQUÍ -->
          <img src="../assets/logo-fix.png
          " alt="iPhone FIX Logo" class="logo-image" />
          <!-- O usa un SVG placeholder -->
          <!-- <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
            <rect width="64" height="64" rx="16" fill="url(#gradient)" />
            <defs>
              <linearGradient id="gradient" x1="0" y1="0" x2="64" y2="64">
                <stop offset="0%" stop-color="#dc2626" />
                <stop offset="100%" stop-color="#991b1b" />
              </linearGradient>
            </defs>
          </svg> -->
        </div>
        <h1 class="brand-title">iPhone FIX</h1>
        <p class="brand-subtitle">Sistema de Gestión</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="login" class="login-form">
        <div class="form-group">
          <label for="email" class="form-label">Correo electrónico</label>
          <input 
            id="email"
            v-model="email" 
            type="email" 
            class="form-input"
            placeholder="correo@ejemplo.com"
            required 
            autocomplete="email"
          />
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Contraseña</label>
          <input 
            id="password"
            v-model="password" 
            type="password" 
            class="form-input"
            placeholder="••••••••"
            required 
            autocomplete="current-password"
          />
        </div>

        <!-- Error Alert -->
        <div v-if="error" class="alert-error">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.5"/>
            <path d="M9 5v4m0 3h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span>{{ error }}</span>
        </div>

        <button type="submit" class="btn-primary" :disabled="isLoading">
          <span v-if="!isLoading">Iniciar Sesión</span>
          <span v-else class="spinner"></span>
        </button>
      </form>

      <!-- Footer -->
      <div class="card-footer">
        <p>Sistema de gestión empresarial</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import http from '../shared/api/http'
import { setToken, setUser } from '../auth/auth'

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
    
    setToken(data.access_token)
    setUser(data.user)
    router.push('/dashboard')
  } catch (err: any) {
    error.value = err?.response?.data?.message || 'Credenciales incorrectas'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Container */
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
  padding: 1rem;
  position: relative;
  overflow: hidden;
}

/* Subtle Background Pattern */
.background-pattern {
  position: absolute;
  inset: 0;
  background-image: 
    linear-gradient(rgba(220, 38, 38, 0.02) 1px, transparent 1px),
    linear-gradient(90deg, rgba(220, 38, 38, 0.02) 1px, transparent 1px);
  background-size: 50px 50px;
  pointer-events: none;
}

/* Login Card */
.login-card {
  background: white;
  border-radius: 16px;
  box-shadow: 
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
  padding: 3rem 2.5rem;
  width: 100%;
  max-width: 420px;
  position: relative;
  animation: slideUp 0.4s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Brand Section */
.brand-section {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo-wrapper {
  display: inline-flex;
  margin-bottom: 1.25rem;
}

/* 🎨 Estilos para tu logo/imagen */
.logo-image {
  width: 80px;
  height: 80px;
  object-fit: contain;
  filter: drop-shadow(0 4px 12px rgba(220, 38, 38, 0.15));
}

/* Si usas SVG en lugar de imagen */
.logo-wrapper svg {
  filter: drop-shadow(0 4px 12px rgba(220, 38, 38, 0.15));
}

.brand-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
}

.brand-subtitle {
  color: #737373;
  font-size: 0.95rem;
  margin: 0;
  font-weight: 400;
}

/* Form Styles */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #525252;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 1.5px solid #e5e5e5;
  border-radius: 8px;
  font-size: 0.9375rem;
  color: #1a1a1a;
  background: white;
  transition: all 0.2s ease;
  outline: none;
}

.form-input::placeholder {
  color: #a3a3a3;
}

.form-input:focus {
  border-color: #dc2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

/* Primary Button */
.btn-primary {
  width: 100%;
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
  border: none;
  border-radius: 8px;
  color: white;
  font-size: 0.9375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
  transform: translateY(-1px);
}

.btn-primary:active:not(:disabled) {
  transform: translateY(0);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Spinner */
.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Error Alert */
.alert-error {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  color: #991b1b;
  font-size: 0.875rem;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.alert-error svg {
  flex-shrink: 0;
}

/* Footer */
.card-footer {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e5e5e5;
}

.card-footer p {
  color: #737373;
  font-size: 0.8125rem;
  margin: 0;
}

/* Responsive */
@media (max-width: 480px) {
  .login-card {
    padding: 2rem 1.5rem;
  }

  .brand-title {
    font-size: 1.5rem;
  }

  .brand-subtitle {
    font-size: 0.875rem;
  }

  .logo-image {
    width: 64px;
    height: 64px;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .login-container {
    background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
  }

  .background-pattern {
    background-image: 
      linear-gradient(rgba(220, 38, 38, 0.05) 1px, transparent 1px),
      linear-gradient(90deg, rgba(220, 38, 38, 0.05) 1px, transparent 1px);
  }

  .login-card {
    background: #262626;
    box-shadow: 
      0 4px 6px -1px rgba(0, 0, 0, 0.3),
      0 2px 4px -1px rgba(0, 0, 0, 0.2);
  }

  .brand-title {
    color: #fafafa;
  }

  .brand-subtitle {
    color: #a3a3a3;
  }

  .form-label {
    color: #d4d4d4;
  }

  .form-input {
    background: #1a1a1a;
    border-color: #404040;
    color: #fafafa;
  }

  .form-input:focus {
    border-color: #dc2626;
  }

  .card-footer {
    border-top-color: #404040;
  }

  .card-footer p {
    color: #a3a3a3;
  }
}
</style>