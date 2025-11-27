<template>
    <div class="page-visit-counter">
        <span class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
            <Icon name="eye" class="w-4 h-4" />
            <span v-if="loading">Cargando...</span>
            <span v-else>{{ formatNumber(visits) }} {{ visits === 1 ? 'visita' : 'visitas' }}</span>
        </span>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

// Props
const props = defineProps({
    url: {
        type: String,
        default: null
    },
    refreshInterval: {
        type: Number,
        default: 5000 // 5 segundos para testing
    }
})

const page = usePage()

// Estado reactivo
const visits = ref(0)
const loading = ref(true)
let intervalId = null

// Función para obtener el contador de visitas
const fetchVisitCount = async () => {
    try {
        // Obtener la ruta relativa en lugar de la URL completa
        let targetPath = props.url
        
        if (!targetPath) {
            // Si no se proporciona URL, extraer la ruta de la URL actual
            const currentUrl = window.location.href
            const urlObj = new URL(currentUrl)
            targetPath = urlObj.pathname
        }
        
        // Normalizar la ruta
        targetPath = '/' + targetPath.replace(/^\/+/, '')
        if (targetPath === '//' || targetPath === '') {
            targetPath = '/'
        }
        
        console.log('Fetching visit count for path:', targetPath)
        
        const response = await axios.get('/api/page-visits/count', {
            params: { 
                url: window.location.href, // Enviar la URL completa para el parsing
                _t: Date.now() // Anti-cache timestamp
            },
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            }
        })
        
        console.log('API Response:', response.data)
        visits.value = parseInt(response.data.visits) || 0
        loading.value = false
        console.log('Visit count updated:', visits.value, 'for path:', targetPath)
    } catch (error) {
        console.error('Error al obtener contador de visitas:', error)
        console.error('Error details:', error.response?.data)
        loading.value = false
        visits.value = 0 // Fallback a 0 en caso de error
    }
}

// Función para formatear números
const formatNumber = (num) => {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M'
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K'
    }
    return num.toString()
}

// Componente de icono simple
const Icon = {
    name: 'Icon',
    props: ['name', 'class'],
    template: `
        <svg :class="$props.class" fill="currentColor" viewBox="0 0 20 20">
            <path v-if="name === 'eye'" d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
            <path v-if="name === 'eye'" fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
        </svg>
    `
}

// Lifecycle hooks
onMounted(() => {
    fetchVisitCount()
    
    // Configurar actualización periódica si se especifica
    if (props.refreshInterval > 0) {
        intervalId = setInterval(fetchVisitCount, props.refreshInterval)
    }
})

// Watch para cambios de página en Inertia
watch(() => page.url, () => {
    console.log('Page changed, updating visit count')
    fetchVisitCount()
}, { immediate: false })

// Cleanup
onBeforeUnmount(() => {
    if (intervalId) {
        clearInterval(intervalId)
    }
})
</script>

<style scoped>
.page-visit-counter {
    display: inline-flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
}

.dark .page-visit-counter {
    color: #9ca3af;
}
</style>