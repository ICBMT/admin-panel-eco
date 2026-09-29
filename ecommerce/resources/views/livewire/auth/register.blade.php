<x-layouts::auth :title="__('Register')">

    <div class="mx-auto w-full max-w-md">

        {{-- Header --}}
        <div class="mb-8 text-center">

            <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-900 text-lg font-bold text-white">
                E
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ __('Create your account') }}
            </h1>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Create an account to start shopping.') }}
            </p>

        </div>


        {{-- Register Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-8">

            {{-- Session Status --}}
            <x-auth-session-status
                class="mb-6 text-center"
                :status="session('status')"
            />


            <form
                method="POST"
                action="{{ route('register.store') }}"
                class="space-y-5"
            >
                @csrf


                {{-- Name --}}
                <flux:input
                    name="name"
                    :label="__('Name')"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Full name')"
                />


                {{-- Email --}}
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                />


                {{-- Password --}}
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Create a password')"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    viewable
                />


                {{-- Confirm Password --}}
                <flux:input
                    name="password_confirmation"
                    :label="__('Confirm password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Confirm your password')"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    viewable
                />


                {{-- Register --}}
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full"
                    data-test="register-user-button"
                >
                    {{ __('Create account') }}
                </flux:button>

            </form>

        </div>


        {{-- Login --}}
        <div class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">

            <span>
                {{ __('Already have an account?') }}
            </span>

            <flux:link
                :href="route('login')"
                wire:navigate
                class="font-semibold"
            >
                {{ __('Log in') }}
            </flux:link>

        </div>

    </div>

</x-layouts::auth>