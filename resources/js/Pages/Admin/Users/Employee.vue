<template>
    <Toast />
    <AppLayout title="Gestionar Personal" :icon="'fa-solid fa-users'">
        <div class="mt-4 bg-white shadow-md p-4 rounded-lg">
            <div class="flex justify-end mb-4">
                <Button @click="modalVisible = true">
                    Crear Horario
                </Button>

            </div>
        </div>

        <DataTable :value="users">
            <Column field="id" header="ID"></Column>
            <Column field="name" header="Nombre"></Column>
            <Column field="email" header="Email"></Column>
            <Column field="schedule_id" header="Horario asignado">
                <template #body="{ data }">
                    <Select
                        @valueChange="(e) => {
                            console.log(e);
                            data.schedule_id = e;
                            updateUserSchedule(data.id, e);
                        }"
                        v-model="data.schedule_id"
                        placeholder="Seleccionar Horario"
                        :options="schedules"
                        optionLabel="name"
                        optionValue="id" 
                        />
                </template>
            </Column>
        </DataTable>


        <Dialog v-model:visible="modalVisible" header="Crear Horario" :modal="true" >
            <div class="p-2">
                <FloatLabel variant="on" class="mb-4">
                    <InputText
                        class="w-full"
                        v-model="name"
                        />
                    <label for="">Nombre</label>
                </FloatLabel>
                <div class="grid grid-cols-2 gap-4">
                    <FloatLabel variant="on">
                        <DatePicker v-model="start_hour" timeOnly fluid />
                        <label for="">Hora inicio</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <DatePicker v-model="end_hour" timeOnly fluid />
                        <label for="">Hora final</label>
                    </FloatLabel>

                </div>

                <div class="flex justify-end mt-4">
                    <Button
                        label="Guardar"
                        icon="pi pi-check"
                        @click="saveSchedule"
                    />
                </div>
            </div>
            

        </Dialog>
    </AppLayout>
</template>
<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted } from "vue";
import { Button, Dialog, FloatLabel, DatePicker, InputText, Toast, useToast, DataTable, Column, Select } from "primevue"
const props = defineProps({
    users : Array
})

const toast = useToast();

const start_hour = ref(null);
const end_hour = ref(null);
const name = ref(null);
let modalVisible = ref(false);
const schedules = ref([]);

const updateUserSchedule = (userId, scheduleId) => {
    axios.post(route('schedules.user.update'), {
        schedule_id: scheduleId,
        user_id: userId
    }).then(response => {
        console.log(response.data);
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Horario actualizado correctamente',
            life: 3000
        });
    }).catch(error => {
        console.error(error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Error al actualizar el horario',
            life: 3000
        });
    })
}

const saveSchedule = () => {
    axios.post(route('schedules.store'), {
        start_hour: start_hour.value?.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }),
        end_hour: end_hour.value?.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }),
        name: name.value
    }).then(response => {
        console.log(response.data);
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Horario creado correctamente',
            life: 3000
        });
    }).catch(error => {
        console.error(error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Error al crear el horario',
            life: 3000
        });
    }).finally(() => {
        fetchSchedules();
        modalVisible.value = false;
        start_hour.value = null;
        end_hour.value = null;
        name.value = null;
    });
}

const fetchSchedules = () => {
    axios.get(route('schedules.list'))
    .then(response => {
        schedules.value = response.data;
        
    }).catch(error => {
        console.log(error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Error al cargar los horarios',
            life: 3000
        });
    })

}

onMounted(() => {
    fetchSchedules();
});
</script>