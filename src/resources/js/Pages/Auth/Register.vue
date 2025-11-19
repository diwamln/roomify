<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-950 flex items-center justify-center relative overflow-hidden py-12">
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

        <!-- Register Card -->
        <div class="relative z-10 w-full max-w-md px-6">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="/logo-v1.png" alt="Roomify Logo" class="h-20 w-auto mx-auto mb-4" />
                <h2 class="text-2xl font-bold text-white">Create your account</h2>
                <p class="text-indigo-200 text-sm mt-2">Join Roomify and start booking rooms</p>
            </div>

            <!-- Register Form Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <form @submit.prevent="submit">
                    <!-- Name Field -->
                    <div>
                        <InputLabel for="name" value="Name" class="text-gray-700 font-medium" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Email Field -->
                    <div class="mt-4">
                        <InputLabel for="email" value="Email" class="text-gray-700 font-medium" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                            v-model="form.email"
                            required
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
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mt-4">
                        <InputLabel
                            for="password_confirmation"
                            value="Confirm Password"
                            class="text-gray-700 font-medium"
                        />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex items-center justify-between">
                        <Link
                            :href="route('login')"
                            class="text-sm text-indigo-600 hover:text-indigo-800 underline"
                        >
                            Already registered?
                        </Link>

                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-white border-2 border-gray-900 rounded-full font-semibold text-sm text-gray-900 uppercase tracking-widest hover:bg-gray-900 hover:text-white focus:bg-gray-900 focus:text-white active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-200"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            Register
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="mt-6 text-center">
                        <span class="text-sm text-gray-600">Have an account? </span>
                        <Link
                            :href="route('login')"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold"
                        >
                            Sign in
                        </Link>
                    </div>
                </form>
            </div>

            <!-- Terms Notice -->
            <p class="text-center text-sm text-indigo-200 mt-6">
                By signing up, you agree to our 
                <a href="#" class="underline hover:text-white">Terms of Service</a> 
                and 
                <a href="#" class="underline hover:text-white">Privacy Policy</a>
            </p>
        </div>
    </div>
</template>