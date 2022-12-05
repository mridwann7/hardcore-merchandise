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

const bandInput = ref(null);
const nameInput = ref(null);
const stockInput = ref(null);
const priceInput = ref(null);
const band = ref(null);

const form = useForm({
    band: null,
    name: null,
    stock: null,
    price: null,
});

const updateData = (accessory) => {
    form.band = band.value ? band.value.id : accessory.accessory_band_id;
    form.name = accessory.name;
    form.stock = accessory.stock;
    form.price = accessory.price;
    form.put(route("accessories.update", accessory.id), {
        errorBag: "updateData",
        preserveScroll: true,
        onError: () => {
            if (form.errors.band) {
                form.reset("band");
                bandInput.value.focus();
            }
            if (form.errors.name) {
                form.reset("name");
                nameInput.value.focus();
            }
            if (form.errors.stock) {
                form.reset("stock");
                stockInput.value.focus();
            }
            if (form.errors.price) {
                form.reset("price");
                priceInput.value.focus();
            }
        },
    });
};

const chooseBand = (accessoryBand) => {
    band.value = accessoryBand;
};

defineProps({
    bands: Object,
    accessory: Object,
});
</script>

<template>
    <AppLayout title="Accessories">
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Accessory Management
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <FormSection @submitted="updateData(accessory)">
                <template #title>Edit Accessory</template>

                <template #description> Edit a accessory data. </template>

                <template #form>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="band" value="Accessory Band" />
                        <div class="dropdown relative mt-2 w-full">
                            <button
                                ref="bandInput"
                                class="dropdown-toggle w-full px-6 py-3 bg-zinc-800 text-white font-medium text-sm leading-tight capitalize rounded shadow-md hover:bg-zinc-900 hover:shadow-lg focus:bg-zinc-900 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-zinc-800 active:shadow-lg active:text-white transition duration-150 ease-in-out flex items-center whitespace-nowrap"
                                type="button"
                                id="band"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                {{
                                    band
                                        ? band.name
                                        : accessory.accessory_band_name
                                }}
                                <div class="w-full flex justify-end">
                                    <svg
                                        aria-hidden="true"
                                        focusable="false"
                                        data-prefix="fas"
                                        data-icon="caret-down"
                                        class="w-2 ml-2"
                                        role="img"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 320 512"
                                    >
                                        <path
                                            fill="currentColor"
                                            d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"
                                        ></path>
                                    </svg>
                                </div>
                            </button>
                            <ul
                                class="dropdown-menu min-w-max absolute w-full bg-white text-base z-50 float-left py-2 list-none text-left rounded-lg shadow-lg mt-1 hidden m-0 bg-clip-padding border-none"
                                aria-labelledby="dropdown_type"
                            >
                                <li
                                    class="dropdown-item text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-gray-700 hover:bg-gray-100"
                                    v-for="band in bands"
                                    @click="chooseBand(band)"
                                >
                                    {{ band.name }}
                                </li>
                            </ul>
                        </div>
                        <InputError :message="form.errors.band" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="name" value="Accessory Name" />
                        <TextInput
                            id="name"
                            ref="nameInput"
                            v-model="accessory.name"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="stock" value="Accessory Stock" />
                        <TextInput
                            id="stock"
                            ref="stockInput"
                            v-model="accessory.stock"
                            type="number"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.stock" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="price" value="Accessory Price" />
                        <TextInput
                            id="price"
                            ref="priceInput"
                            v-model="accessory.price"
                            type="number"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.price" class="mt-2" />
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
