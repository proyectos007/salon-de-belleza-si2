<script setup>
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import { useForm, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue";

const page = usePage();
const form = useForm({
    id: page?.props?.user?.id,
    name: page?.props?.user?.name,
    email: page?.props?.user?.email,
    role_id: null,
    password: null,
});
const toast = useToast();

const props = defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(["update:modelValue"]);

const submit = () => {
    form.role_id = page.props?.user?.roles[0]?.id;

    form.put(route("admin.users.update", form.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Éxito",
                detail: "Usuario actualizado correctamente",
                life: 5000,
            });
            emit("update:modelValue", false);
            form.reset();
        },
        onError: (error) => {
            Object.values(error)
                .flat()
                .forEach((message) => {
                    toast.add({
                        severity: "error",
                        summary: "Error",
                        detail: message,
                        life: 5000,
                    });
                });
        },
    });
};
</script>

<template>
    <Dialog
        :visible="modelValue"
        @update:visible="emit('update:modelValue', $event)"
        header="Editar Perfil"
        :style="{ width: '30rem' }"
    >
        <form @submit.prevent="submit">
            <div class="flex flex-col gap-2 mb-2">
                <label for="password">Nombre</label>
                <InputText
                    v-model="form.name"
                    placeholder="Nombre del rol"
                    class="w-full"
                />
            </div>
            <div class="flex flex-col gap-2 mb-2">
                <label for="password">Correo</label>
                <InputText
                    v-model="form.email"
                    placeholder="Correo"
                    class="w-full"
                />
            </div>
            <div class="flex flex-col gap-2 mb-2">
                <label for="password">Contraseña</label>
                <InputText
                    id="password"
                    type="password"
                    placeholder="Contraseña"
                    v-model="form.password"
                />
            </div>
            <div class="flex justify-between py-2">
                <Button
                    label="Cancelar"
                    @click="emit('update:modelValue', false)"
                    :loading="form.processing"
                />
                <Button
                    label="Editar"
                    type="submit"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>
</template>
