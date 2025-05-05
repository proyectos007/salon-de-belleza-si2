<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { format } from "date-fns";
import { es } from "date-fns/locale";
import { Column, DataTable } from "primevue";

defineProps({ logs: Object });

function formatDate(log) {
    return format(new Date(log.created_at), "PPpp", { locale: es });
}
</script>

<template>
    <AppLayout title="Bitácora de Actividades">
        <div class="mt-4 bg-white shadow-md p-4 rounded-lg">
            <div class="card">
                <DataTable
                    :value="logs.data"
                    paginator
                    :rows="10"
                    :totalRecords="logs.total"
                    :rowsPerPageOptions="[10, 20, 50]"
                    tableStyle="min-width: 50rem"
                    :lazy="true"
                    responsiveLayout="scroll"
                >
                    <Column field="id" header="ID" />
                    <Column
                        field="created_at"
                        header="Fecha y Hora"
                        :body="formatDate"
                    />
                    <Column header="Usuario">
                        <template #body="{ data }">{{
                            data?.user?.name
                        }}</template>
                    </Column>

                    <Column field="action" header="Acción" />
                    <Column field="ip_address" header="IP" />
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
