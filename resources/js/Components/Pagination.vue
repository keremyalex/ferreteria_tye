<template>
  <nav v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6" role="navigation" aria-label="Pagination Navigation">
    <div class="flex justify-between flex-1 sm:hidden">
      <Component
        :is="links[0].url ? Link : 'span'"
        :href="links[0].url || ''"
        :class="[
          'relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
          links[0].url
            ? 'text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700'
            : 'text-gray-400 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 cursor-not-allowed'
        ]"
      >
        {{ $t('Anterior') }}
      </Component>
      
      <Component
        :is="links[links.length - 1].url ? Link : 'span'"
        :href="links[links.length - 1].url || ''"
        :class="[
          'relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium rounded-md',
          links[links.length - 1].url
            ? 'text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700'
            : 'text-gray-400 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 cursor-not-allowed'
        ]"
      >
        {{ $t('Siguiente') }}
      </Component>
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          {{ $t('Mostrando') }}
          <span class="font-medium">{{ from }}</span>
          {{ $t('a') }}
          <span class="font-medium">{{ to }}</span>
          {{ $t('de') }}
          <span class="font-medium">{{ total }}</span>
          {{ $t('resultados') }}
        </p>
      </div>
      
      <div>
        <span class="relative z-0 inline-flex shadow-sm rounded-md">
          <!-- Botón Anterior -->
          <Component
            :is="links[0].url ? Link : 'span'"
            :href="links[0].url || ''"
            :class="[
              'relative inline-flex items-center px-2 py-2 rounded-l-md border text-sm font-medium',
              links[0].url
                ? 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                : 'border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-gray-300 dark:text-gray-600 cursor-not-allowed'
            ]"
            :title="$t('Página anterior')"
          >
            <ChevronLeftIcon class="h-5 w-5" />
          </Component>

          <!-- Números de página -->
          <template v-for="(link, index) in links.slice(1, -1)" :key="index">
            <Component
              :is="link.url ? Link : 'span'"
              :href="link.url || ''"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                link.active
                  ? 'z-10 bg-indigo-50 dark:bg-indigo-900 border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-200'
                  : link.url
                    ? 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                    : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-300 dark:text-gray-600 cursor-not-allowed',
                index === 0 ? '' : '-ml-px'
              ]"
              v-html="link.label"
            />
          </template>

          <!-- Botón Siguiente -->
          <Component
            :is="links[links.length - 1].url ? Link : 'span'"
            :href="links[links.length - 1].url || ''"
            :class="[
              'relative inline-flex items-center px-2 py-2 rounded-r-md border text-sm font-medium -ml-px',
              links[links.length - 1].url
                ? 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                : 'border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 text-gray-300 dark:text-gray-600 cursor-not-allowed'
            ]"
            :title="$t('Página siguiente')"
          >
            <ChevronRightIcon class="h-5 w-5" />
          </Component>
        </span>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
  links: {
    type: Array,
    required: true
  },
  from: {
    type: Number,
    default: 0
  },
  to: {
    type: Number,
    default: 0
  },
  total: {
    type: Number,
    default: 0
  }
})

// Extraer información de paginación desde los links si no se proporciona directamente
const from = computed(() => {
  if (props.from) return props.from
  
  // Intentar extraer desde los links si están en formato Laravel
  const currentPageLink = props.links.find(link => link.active)
  if (currentPageLink && props.total) {
    const currentPage = parseInt(currentPageLink.label)
    const perPage = Math.ceil(props.total / props.links.length - 2) || 10
    return ((currentPage - 1) * perPage) + 1
  }
  return 1
})

const to = computed(() => {
  if (props.to) return props.to
  
  // Intentar calcular basado en la página actual
  const currentPageLink = props.links.find(link => link.active)
  if (currentPageLink && props.total) {
    const currentPage = parseInt(currentPageLink.label)
    const perPage = Math.ceil(props.total / props.links.length - 2) || 10
    return Math.min(currentPage * perPage, props.total)
  }
  return props.total
})

const total = computed(() => props.total || 0)
</script>