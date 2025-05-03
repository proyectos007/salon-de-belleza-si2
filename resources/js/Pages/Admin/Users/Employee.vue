<template>
    <AppLayout title="Gestionar Personal" :icon="'fa-solid fa-users'">
        <div class="mt-4 bg-white shadow-md p-4 rounded-lg">
            {{ users }}
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    users : Array
})
</script>