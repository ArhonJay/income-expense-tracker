<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: '',
        description: '',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div class="mb-6">
        <h2 class="text-lg font-medium">Expense and Income Tracker</h2>
        <p class="text-sm text-muted-foreground">Please sign in</p>
    </div>

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2 border-b border-gray-200 pb-2">
                <Label
                    for="email"
                    class="text-xs tracking-wider text-gray-500 uppercase"
                    >Username</Label
                >
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    class="h-8 border-0 px-0 shadow-none focus-visible:ring-0"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2 border-b border-gray-200 pb-2">
                <Label
                    for="password"
                    class="text-xs tracking-wider text-gray-500 uppercase"
                    >Password</Label
                >
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    class="h-8 border-0 px-0 shadow-none focus-visible:ring-0"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="mt-2 flex items-center justify-between">
                <Label
                    for="remember"
                    class="flex items-center space-x-3 text-sm font-normal text-gray-600"
                >
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Keep me logged in</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 flex w-32 items-center gap-2 bg-[#5cb85c] text-white hover:bg-[#4cae4c]"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                <svg
                    v-if="!processing"
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="lucide lucide-log-in"
                >
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    <polyline points="10 17 15 12 10 7" />
                    <line x1="15" x2="3" y1="12" y2="12" />
                </svg>
                Sign In
            </Button>

            <div class="mt-4">
                <TextLink
                    v-if="canResetPassword"
                    :href="request()"
                    class="flex items-center text-sm text-red-500 hover:text-red-600"
                    :tabindex="5"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="14"
                        height="14"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-triangle-alert mr-1"
                    >
                        <path
                            d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"
                        />
                        <path d="M12 9v4" />
                        <path d="M12 17h.01" />
                    </svg>
                    Forgot Password
                </TextLink>
            </div>
        </div>

        <!-- removed the sign up link to match the screenshot -->
    </Form>
</template>
