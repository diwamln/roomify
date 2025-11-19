<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-950 flex items-center justify-center relative overflow-hidden">
        <!-- Stars Background -->
        <div class="absolute inset-0">
            <!-- Large stars -->
            <div class="absolute top-20 left-10 w-1 h-1 bg-white rounded-full animate-pulse"></div>
            <div class="absolute top-40 right-32 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
            <div class="absolute top-60 left-1/4 w-1.5 h-1.5 bg-white rounded-full animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute bottom-40 right-1/4 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 1.5s"></div>
            <div class="absolute top-32 right-1/3 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 2s"></div>
            <div class="absolute bottom-60 left-1/3 w-1.5 h-1.5 bg-white rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
            <div class="absolute top-1/2 right-20 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 1.2s"></div>
            <div class="absolute top-1/3 left-1/2 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 0.3s"></div>
            <div class="absolute bottom-1/4 right-1/2 w-1.5 h-1.5 bg-white rounded-full animate-pulse" style="animation-delay: 1.8s"></div>
            
            <!-- Small stars -->
            <div class="absolute top-24 right-1/2 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-48 left-20 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute bottom-32 right-40 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-72 right-1/4 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute bottom-48 left-1/4 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-44 left-1/3 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute bottom-52 right-1/3 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-36 right-16 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-80 left-16 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute bottom-24 left-1/2 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-16 left-40 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute bottom-16 right-60 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-2/3 left-20 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute bottom-1/3 right-20 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
            <div class="absolute top-1/4 right-80 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
        </div>

        <!-- Login Card -->
        <div class="relative z-10 w-full max-w-md px-6">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="/logo-v1.png" alt="Roomify Logo" class="h-20 w-auto mx-auto mb-4" />
            </div>

            <!-- Login Form Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <!-- Email Field -->
                    <div>
                        <InputLabel for="email" value="Email" class="text-gray-700 font-medium" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password Field -->
                    <div class="mt-4">
                        <InputLabel for="password" value="Password" class="text-gray-700 font-medium" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between mt-6">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-indigo-600 hover:text-indigo-800 underline"
                        >
                            Forgot your password?
                        </Link>

                        <button
                            type="submit"
                            class="ms-auto inline-flex items-center px-6 py-2.5 bg-white border-2 border-gray-700 rounded-full font-semibold text-sm text-gray-700 uppercase tracking-widest hover:bg-gray-700 hover:text-white focus:bg-gray-700 focus:text-white active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-200"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            LOG IN
                        </button>
                    </div>

                    <!-- Register Link -->
                    <div class="mt-6 text-center">
                        <span class="text-sm text-gray-600">Don't have an account? </span>
                        <Link
                            :href="route('register')"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold"
                        >
                            Sign up
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>