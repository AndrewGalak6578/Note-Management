<script setup>
const props = defineProps({
    notes: Array,
    loading: Boolean,
});

const emit = defineEmits(["edit", "delete"]);

function formatDate(iso) {
    if (!iso) return "";
    const d = new Date(iso);
    return new Intl.DateTimeFormat("ru-RU", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    }).format(d);
}

function fromNow(iso) {
    if (!iso) return "";
    const d = new Date(iso);
    const diffMs = Date.now() - d.getTime();
    const sec = Math.floor(diffMs / 1000);
    const min = Math.floor(sec / 60);
    const hr = Math.floor(min / 60);
    const day = Math.floor(hr / 24);

    if (sec < 60) return `${sec} сек назад`;
    if (min < 60) return `${min} мин назад`;
    if (hr < 24) return `${hr} ч назад`;
    return `${day} д назад`;
}
</script>

<template>
    <div v-if="loading">Loading...</div>

    <div v-else style="display: flex; flex-direction: column; gap: 10px;">
        <div v-if="notes.length === 0" style="opacity: 0.7;">No notes yet</div>

        <div
            v-for="n in notes"
            :key="n.id"
            style="border: 1px solid #eee; padding: 10px; border-radius: 10px;"
        >
            <div style="display:flex; justify-content: space-between; gap: 8px;">
                <strong>#{{ n.id }} — {{ n.title }}</strong>
                <div style="display:flex; gap: 8px;">
                    <button
                        @click="emit('edit', n)"
                        style="border: 1px solid #aaa; border-radius: 8px; padding: 6px 10px;"
                    >
                        Edit
                    </button>
                    <button
                        @click="emit('delete', n)"
                        style="border: 1px solid #f87171; border-radius: 8px; padding: 6px 10px;"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <div v-if="n.content" style="margin-top: 8px; white-space: pre-wrap;">
                {{ n.content }}
            </div>

            <div style="margin-top: 8px; opacity: 0.6; font-size: 12px;">
                {{ formatDate(n.created_at) }} • {{ fromNow(n.created_at) }}
            </div>
        </div>
    </div>
</template>
