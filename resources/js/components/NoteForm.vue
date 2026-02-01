<script setup>
const props = defineProps({
    modelValue: Object,
    loading: Boolean,
});

const emit = defineEmits(["update:modelValue", "submit", "cancel"]);

function updateField(key, value) {
    emit("update:modelValue", { ...props.modelValue, [key]: value });
}
</script>

<template>
    <div style="border: 1px solid #ddd; padding: 12px; border-radius: 10px;">
        <h3 style="margin: 0 0 10px;">
            {{ modelValue.id ? `Edit #${modelValue.id}` : "Create note" }}
        </h3>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <input
                :value="modelValue.title"
                @input="updateField('title', $event.target.value)"
                placeholder="Title"
                style="padding: 10px; border: 1px solid #ccc; border-radius: 8px;"
            />

            <textarea
                :value="modelValue.content"
                @input="updateField('content', $event.target.value)"
                placeholder="Content (optional)"
                rows="7"
                style="padding: 10px; border: 1px solid #ccc; border-radius: 8px; resize: vertical;"
            ></textarea>

            <div style="display: flex; gap: 8px;">
                <button
                    @click="emit('submit')"
                    :disabled="loading"
                    style="padding: 10px 12px; border-radius: 8px; border: 1px solid #111;"
                >
                    {{ modelValue.id ? "Update" : "Create" }}
                </button>

                <button
                    v-if="modelValue.id"
                    @click="emit('cancel')"
                    :disabled="loading"
                    style="padding: 10px 12px; border-radius: 8px; border: 1px solid #aaa;"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>
