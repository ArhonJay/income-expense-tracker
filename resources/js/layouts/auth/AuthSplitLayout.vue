<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh flex-col items-center justify-center bg-white px-8 sm:px-0 lg:max-w-none lg:grid-cols-5 lg:px-0"
    >
        <div
            class="relative hidden h-full flex-col bg-muted bg-cover bg-center p-10 text-white lg:col-span-3 lg:flex"
            style="background-image: url('/images/library_background.png')"
        ></div>
        <div class="lg:col-span-2 lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
