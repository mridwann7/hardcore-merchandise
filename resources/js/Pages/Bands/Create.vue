<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const nameInput = ref(null);
const genreInput = ref(null);
const debutInput = ref(null);

const form = useForm({
    name: null,
    genre: null,
    debut: null,
});

const storeData = () => {
    form.post(route("bands.store"), {
        errorBag: "storeData",
        preserveScroll: true,
        onError: () => {
            if (form.errors.name) {
                form.reset("name");
                nameInput.value.focus();
            }
            if (form.errors.genre) {
                form.reset("genre");
                genreInput.value.focus();
            }
            if (form.errors.debut) {
                form.reset("debut");
                debutInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <AppLayout title="Bands">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Band Management
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <FormSection @submitted="storeData">
                <template #title>Add Band</template>

                <template #description>
                    Add a new band data to database.
                </template>

                <template #form>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="name" value="Band Name" />
                        <TextInput
                            id="name"
                            ref="nameInput"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="genre" value="Band Genre" />
                        <TextInput
                            id="genre"
                            ref="genreInput"
                            v-model="form.genre"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.genre" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="debut" value="Band Debut" />
                        <TextInput
                            id="debut"
                            ref="debutInput"
                            v-model="form.debut"
                            type="number"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.debut" class="mt-2" />
                    </div>
                </template>

                <template #actions>
                    <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                        Saved.
                    </ActionMessage>

                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Save
                    </PrimaryButton>
                </template>
            </FormSection>
        </div>
    </AppLayout>
</template>
