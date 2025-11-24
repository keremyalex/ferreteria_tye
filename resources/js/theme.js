// resources/js/theme.js
import { ref, watchEffect } from 'vue';

const isDark = ref(getInitialTheme());

function getInitialTheme() {
    const stored = localStorage.getItem('theme');
    if (stored === 'dark') return true;
    if (stored === 'light') return false;

    // Por defecto, usar tema claro
    return false;
}

// Se aplica la clase al html y guarda la preferencia
watchEffect(() => {
    const html = document.documentElement;
    if (isDark.value) {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
});

// Función para alternar tema
export function toggleTheme() {
    isDark.value = !isDark.value;
}

// Llama esto una sola vez desde app.js para inicializar
export function setupTheme() {
    // solo fuerza la reactividad inicial
    isDark.value = getInitialTheme();
}

// Llama esto desde cualquier componente para controlar el tema
export function useDarkMode() {
    return { 
        isDark,
        toggleTheme
    };
}