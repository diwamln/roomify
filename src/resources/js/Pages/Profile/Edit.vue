<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <!-- Header -->
        <template #header>
            <h2 class="text-lg font-semibold leading-tight text-gray-800">
                Pengaturan Profil
            </h2>
        </template>

        <!-- FULL WIDTH CONTAINER -->
        <div class="py-8 bg-gray-50 min-h-screen w-full">
            <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
                
                <!-- Card 1: Update Info Profil -->
                <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="max-w-xl">
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Perbarui informasi profil akun dan alamat email Anda.
                            </p>
                        </header>
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <!-- Card 2: Update Password -->
                <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="max-w-xl">
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Perbarui Kata Sandi</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
                            </p>
                        </header>
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <!-- Card 3: Hapus Akun -->
                <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="max-w-xl">
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Hapus Akun</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Setelah akun dihapus, semua sumber daya dan data akan dihapus secara permanen.
                            </p>
                        </header>
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>