<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineProps, ref } from "vue";
import { DataTable, Column, Button } from "primevue";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import { ROLES } from "@/Contants/Role.js";

defineProps({
    clients: Object,
});

const isOpenModal = ref(false);
const toast = useToast();

const createUserForm = useForm({
    name: "",
    email: "",
    role_id: 2,
    password: "",
    password_confirmation: "",
});

const submitCreateUser = () => {
    createUserForm.role_id = ROLES.client;
    createUserForm.post(route("admin.clients.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (response) => {
            console.log(`Respuesta ${JSON.stringify(response)}`);
            toast.add({
                severity: "success",
                summary: "Éxito",
                life: 5000,
                detail: "Usuario creado correctamente",
            });
            isOpenModal.value = false;
            createUserForm.reset();
        },
        onError: (error) => {
            createUserForm.errors = error;

            if (error) {
                Object.entries(error).forEach(([key, messages]) => {
                    if (Array.isArray(messages)) {
                        messages.forEach((message) => {
                            toast.add({
                                severity: "error",
                                summary: "Error",
                                life: 5000,
                                detail: message,
                            });
                        });
                    } else {
                        toast.add({
                            severity: "error",
                            summary: "Error",
                            life: 5000,
                            detail: messages,
                        });
                    }
                });
            } else {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    life: 5000,
                    detail: "Ha ocurrido un error desconocido.",
                });
            }
        },
    });
};
</script>

<template>
    <AppLayout title="Usuarios">
        <div class="mt-4 bg-white shadow-md p-4 rounded-lg">
            <div class="flex justify-between my-5">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestionar Clientes
                </h2>
                <Button
                    label="Crear Usuarios"
                    @click="() => (isOpenModal = true)"
                />
            </div>

            <div class="card">
                <DataTable :value="clients" tableStyle="min-width: 50rem">
                    <Column field="id" header="Codigo"></Column>
                    <Column field="name" header="Nombre"></Column>
                    <Column field="email" header="Correo"></Column>
                    <Column header="Dinero Gastado">
                        <template #body="{ data }">{{ 0 }}</template></Column
                    >
                </DataTable>
            </div>

            <Dialog
                v-model:visible="isOpenModal"
                class="py-5 px-2"
                header="Creando Cliente"
                :style="{ width: '25rem' }"
            >
                <form @submit.prevent="submitCreateUser">
                    <div class="flex flex-col gap-2 mb-2">
                        <label for="name">Nombre</label>
                        <InputText
                            id="name"
                            type="text"
                            placeholder="Nombre del usuario"
                            v-model="createUserForm.name"
                        />
                    </div>
                    <div class="flex flex-col gap-2 mb-2">
                        <label for="email">Correo</label>
                        <InputText
                            id="email"
                            type="email"
                            placeholder="user@correo.com"
                            v-model="createUserForm.email"
                        />
                    </div>

                    <!-- <div class="flex flex-col gap-2 mb-2">
                        <label for="email">Rol</label>
                        <Select
                            v-model="createUserForm.role_id"
                            :options="roles"
                            optionValue="id"
                            optionLabel="name"
                            placeholder="Selecciona el rol"
                            class="w-full"
                        />
                    </div> -->

                    <div class="flex flex-col gap-2 mb-2">
                        <label for="password">Contraseña</label>
                        <InputText
                            id="password"
                            type="password"
                            placeholder="Contraseña"
                            v-model="createUserForm.password"
                        />
                    </div>
                    <div class="flex flex-col gap-2 mb-2">
                        <label for="password">Confirmar Contraseña</label>
                        <InputText
                            id="password"
                            type="password"
                            placeholder="Confirma contraseña"
                            v-model="createUserForm.password_confirmation"
                        />
                    </div>
                    <div class="flex justify-between py-2">
                        <Button
                            label="Cancelar"
                            @click="() => (isOpenModal = false)"
                            :loading="createUserForm.processing"
                        />
                        <Button
                            label="Crear"
                            type="submit"
                            :loading="createUserForm.processing"
                        />
                    </div>
                </form>
            </Dialog>
        </div>
    </AppLayout>
</template>
