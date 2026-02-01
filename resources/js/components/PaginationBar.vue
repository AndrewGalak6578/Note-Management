<script setup>
import {computed} from "vue";

const props = defineProps({
    current: Number,
    last: Number,
    total: Number,
    loading: Boolean,
    pages: Array,
    showFirst: Boolean,
    showLast: Boolean,
});

const emit = defineEmits(["prev", "next", "page"]);

const canPrev = computed(() => props.current > 1 && !props.loading);
const canNext = computed(() => props.current < props.last && !props.loading);
</script>

<template>
    <div>
        <div style="display:flex; justify-content: space-between; align-items:center; margin: 10px 0;">
            <button
                @click="emit('prev')"
                :disabled="!canPrev"
                style="padding: 8px 10px; border-radius: 8px; border: 1px solid #aaa;"
            >
                Prev
            </button>

            <div style="opacity: 0.8;">
                Page {{ current }} / {{ last }} • Total: {{ total }}
            </div>

            <button
                @click="emit('next')"
                :disabled="!canNext"
                style="padding: 8px 10px; border-radius: 8px; border: 1px solid #aaa;"
            >
                Next
            </button>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap: 6px; margin-bottom: 10px;">
            <button
                v-if="showFirst"
                @click="emit('page', 1)"
                :disabled="loading"
                style="padding: 6px 10px; border-radius: 8px; border: 1px solid #aaa;"
            >
                1
            </button>

            <span v-if="showFirst" style="padding: 6px 4px; opacity: 0.7;">…</span>

            <button
                v-for="p in pages"
                :key="p"
                @click="emit('page', p)"
                :disabled="loading"
                :style="{
          padding: '6px 10px',
          borderRadius: '8px',
          border: '1px solid #aaa',
          fontWeight: p === current ? '700' : '400',
          opacity: p === current ? '1' : '0.85'
        }"
            >
                {{ p }}
            </button>

            <span v-if="showLast" style="padding: 6px 4px; opacity: 0.7;">…</span>

            <button
                v-if="showLast"
                @click="emit('page', last)"
                :disabled="loading"
                style="padding: 6px 10px; border-radius: 8px; border: 1px solid #aaa;"
            >
                {{ last }}
            </button>
        </div>
    </div>
</template>
