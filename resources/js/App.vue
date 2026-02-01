<script setup>
import { ref, computed, onMounted } from "vue";
import NoteForm from "./components/NoteForm.vue";
import NotesList from "./components/NotesList.vue";
import PaginationBar from "./components/PaginationBar.vue";

const notes = ref([]);
const meta = ref({ current_page: 1, last_page: 1, per_page: 10, total: 0 });

const loading = ref(false);
const error = ref(null);

const form = ref({ id: null, title: "", content: "" });
const perPage = ref(10);

async function api(path, options = {}) {
    const res = await fetch(path, {
        headers: { Accept: "application/json", "Content-Type": "application/json" },
        ...options,
    });

    if (!res.ok) {
        const body = await res.json().catch(() => ({}));
        const msg = body?.message || `HTTP ${res.status}`;
        throw { status: res.status, body, msg };
    }

    if (res.status === 204) return null;
    return res.json();
}

function setError(e, fallback = "Ошибка") {
    if (!e) return (error.value = fallback);
    if (e.status === 422) {
        const errors = e.body?.errors || {};
        const first = Object.keys(errors)[0];
        return (error.value = first ? errors[first][0] : e.msg);
    }
    error.value = e.msg || fallback;
}

function normalizePaginator(data) {
    notes.value = data?.data ?? [];
    meta.value = data?.meta ?? { current_page: 1, last_page: 1, per_page: perPage.value, total: 0 };
    if (meta.value?.per_page) perPage.value = Number(meta.value.per_page);
}

async function loadNotes(page = 1) {
    loading.value = true;
    error.value = null;
    try {
        const data = await api(`/api/notes?page=${page}&per_page=${perPage.value}`);
        normalizePaginator(data);
    } catch (e) {
        setError(e, "Ошибка загрузки");
    } finally {
        loading.value = false;
    }
}

function startCreate() {
    form.value = { id: null, title: "", content: "" };
}

function startEdit(note) {
    form.value = { id: note.id, title: note.title, content: note.content ?? "" };
}

async function submit() {
    error.value = null;
    try {
        if (!form.value.title.trim()) return (error.value = "Title обязателен");

        const payload = { title: form.value.title, content: form.value.content };

        if (form.value.id) {
            await api(`/api/notes/${form.value.id}`, { method: "PUT", body: JSON.stringify(payload) });
            startCreate();
            await loadNotes(meta.value.current_page);
        } else {
            await api(`/api/notes`, { method: "POST", body: JSON.stringify(payload) });
            startCreate();
            await loadNotes(1);
        }
    } catch (e) {
        setError(e, "Ошибка сохранения");
    }
}

async function removeNote(note) {
    if (!confirm(`Удалить заметку #${note.id}?`)) return;
    error.value = null;
    try {
        await api(`/api/notes/${note.id}`, { method: "DELETE" });

        const nextPage =
            notes.value.length === 1 && meta.value.current_page > 1
                ? meta.value.current_page - 1
                : meta.value.current_page;

        if (form.value.id === note.id) startCreate();
        await loadNotes(nextPage);
    } catch (e) {
        setError(e, "Ошибка удаления");
    }
}

async function changePerPage() {
    await loadNotes(1);
}

const pageWindow = computed(() => {
    const current = meta.value.current_page;
    const last = meta.value.last_page;

    const windowSize = 5;
    const half = Math.floor(windowSize / 2);

    let start = Math.max(1, current - half);
    let end = Math.min(last, start + windowSize - 1);
    start = Math.max(1, end - windowSize + 1);

    const pages = [];
    for (let p = start; p <= end; p++) pages.push(p);

    return { pages, showFirst: start > 1, showLast: end < last };
});

onMounted(async () => {
    startCreate();
    await loadNotes(1);
});
</script>

<template>
    <div style="max-width: 980px; margin: 40px auto; padding: 0 16px; font-family: system-ui;">
        <h1 style="margin-bottom: 16px;">Notes</h1>

        <div
            v-if="error"
            style="margin-bottom: 12px; padding: 10px; border: 1px solid #fca5a5; background: #fee2e2; border-radius: 10px;"
        >
            {{ error }}
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <NoteForm v-model="form" :loading="loading" @submit="submit" @cancel="startCreate" />

            <div style="border: 1px solid #ddd; padding: 12px; border-radius: 10px;">
                <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 10px;">
                    <h3 style="margin:0;">All notes</h3>

                    <div style="display:flex; align-items:center; gap: 8px;">
                        <label style="opacity: 0.8;">per page</label>
                        <select
                            v-model.number="perPage"
                            @change="changePerPage"
                            :disabled="loading"
                            style="padding: 6px 8px; border-radius: 8px; border: 1px solid #aaa;"
                        >
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>

                        <button
                            @click="loadNotes(meta.current_page)"
                            :disabled="loading"
                            style="padding: 8px 10px; border-radius: 8px; border: 1px solid #aaa;"
                        >
                            Refresh
                        </button>
                    </div>
                </div>

                <PaginationBar
                    :current="meta.current_page"
                    :last="meta.last_page"
                    :total="meta.total"
                    :loading="loading"
                    :pages="pageWindow.pages"
                    :show-first="pageWindow.showFirst"
                    :show-last="pageWindow.showLast"
                    @prev="loadNotes(meta.current_page - 1)"
                    @next="loadNotes(meta.current_page + 1)"
                    @page="loadNotes"
                />

                <NotesList :notes="notes" :loading="loading" @edit="startEdit" @delete="removeNote" />
            </div>
        </div>
    </div>
</template>
