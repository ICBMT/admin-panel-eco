<x-layouts::auth :title="__('Log in')">

    <div class="mx-auto w-full max-w-md">

        {{-- Header --}}
        <div class="mb-8 text-center">

            <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-900 text-lg font-bold text-white">
                E
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ __('Welcome back') }}
            </h1>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Log in to continue shopping.') }}
            </p>

        </div>


        {{-- Login Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-8">

            {{-- Session Status --}}
            <x-auth-session-status
                class="mb-6 text-center"
                :status="session('status')"
            />


            <form
                method="POST"
                action="{{ route('login.store') }}"
                class="space-y-5"
            >
                @csrf


                {{-- Email --}}
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                />


                {{-- Password --}}
                <div class="relative">

                    <flux:input
                        name="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Enter your password')"
                        viewable
                    />

                    @if (Route::has('password.request'))

                        <flux:link
                            class="absolute top-0 text-sm end-0"
                            :href="route('password.request')"
                            wire:navigate
                        >
                            {{ __('Forgot password?') }}
                        </flux:link>

                    @endif

                </div>


                {{-- Remember Me --}}
                <flux:checkbox
                    name="remember"
                    :label="__('Remember me')"
                    :checked="old('remember')"
                />


                {{-- Login --}}
                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full"
                    data-test="login-button"
                >
                    {{ __('Log in') }}
                </flux:button>

            </form>

        </div>


        {{-- Register --}}
        <div class="mt-6 text-center text-sm text-zinc-600 dark:text-zinc-400">

            <span>
                {{ __('Don\'t have an account?') }}
            </span>

            <flux:link
                :href="route('register')"
                wire:navigate
                class="font-semibold"
            >
                {{ __('Create an account') }}
            </flux:link>

        </div>

    </div>

</x-layouts::auth>
