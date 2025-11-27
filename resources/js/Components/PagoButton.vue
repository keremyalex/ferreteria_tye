<template>
    <button 
        @click="handleClick"
        :disabled="disabled"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
        <span v-if="cuota === 'primera'">
            Pagar Primera Cuota
        </span>
        <span v-else-if="disabled">
            Primera cuota pendiente
        </span>
        <span v-else>
            Pagar Segunda Cuota
        </span>
    </button>
</template>

<script setup>
const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    cuota: {
        type: String,
        required: true,
        validator: value => ['primera', 'segunda'].includes(value)
    },
    amount: {
        type: Number,
        required: true
    },
    disabled: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['pagar'])

const handleClick = () => {
    if (!props.disabled) {
        emit('pagar', {
            order: props.order,
            cuota: props.cuota,
            amount: props.amount
        })
    }
}
</script>