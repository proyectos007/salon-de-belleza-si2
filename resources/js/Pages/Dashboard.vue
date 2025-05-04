<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Button } from 'primevue';
import Welcome from '@/Components/Welcome.vue';
import { router, Link } from '@inertiajs/vue3';

const backup = () => {
    router.visit('/admin/backup');
}
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="mt-4 bg-white shadow-md p-4 rounded-lg">
            <div class="flex justify-end mb-4">
                <a :href="route('admin.backup')">
                    <Button>
                        Realizar copia de seguridad
                    </Button>
                </a>
            </div>
        </div>
    </AppLayout>
</template>
