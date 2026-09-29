<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{ config('app.name', 'E-Commerce') }}</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    html {
        scroll-behavior: smooth;
    }

    body {
        cursor: none;
    }

    a,
    button {
        cursor: none;
    }

    .glass {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .glass-dark {
        background: rgba(20, 20, 20, 0.45);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.10);
    }

    .cursor-dot {
        position: fixed;
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: white;
        pointer-events: none;
        z-index: 9999;
        transform: translate(-50%, -50%);
    }

    .cursor-ring {
        position: fixed;
        width: 38px;
        height: 38px;
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 9999px;
        pointer-events: none;
        z-index: 9998;
        transform: translate(-50%, -50%);
        transition:
            width 0.25s ease,
            height 0.25s ease,
            background 0.25s ease,
            border-color 0.25s ease;
    }

    .cursor-ring.active {
        width: 62px;
        height: 62px;
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.9);
    }

    .reveal {
        opacity: 0;
        transform: translateY(35px);
        transition:
            opacity 0.9s ease,
            transform 0.9s cubic-bezier(.22, 1, .36, 1);
    }

    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .hero-glow {
        animation: floatGlow 8s ease-in-out infinite;
    }

    @keyframes floatGlow {
        0%, 100% {
            transform: translate3d(0, 0, 0) scale(1);
        }

        50% {
            transform: translate3d(0, -25px, 0) scale(1.05);
        }
    }

    .story-image {
        transition: transform 1.2s cubic-bezier(.22, 1, .36, 1);
    }

    .story-card:hover .story-image {
        transform: scale(1.05);
    }
</style>

</head>

<body class="min-h-screen overflow-x-hidden bg-[#080808] text-white">

{{-- Custom Cursor --}}
<div class="cursor-dot hidden md:block"></div>
<div class="cursor-ring hidden md:block"></div>


{{-- Navigation --}}
<nav class="fixed inset-x-0 top-0 z-50 px-4 py-4 sm:px-6">

    <div class="glass mx-auto flex max-w-7xl items-center justify-between rounded-2xl px-5 py-3">

        <a
            href="{{ url('/') }}"
            class="text-lg font-semibold tracking-tight"
        >
            {{ config('app.name', 'Store') }}
        </a>

        <div class="flex items-center gap-2">

            <a
                href="{{ route('products.index') }}"
                class="rounded-xl px-4 py-2 text-sm text-white/70 transition hover:bg-white/10 hover:text-white"
            >
                Shop
            </a>

            @auth
                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-xl px-4 py-2 text-sm text-white/70 transition hover:bg-white/10 hover:text-white"
                >
                    Dashboard
                </a>
            @else
                @if (Route::has('login'))
                    <a
                        href="{{ route('login') }}"
                        class="rounded-xl px-4 py-2 text-sm text-white/70 transition hover:bg-white/10 hover:text-white"
                    >
                        Login
                    </a>
                @endif

                @if (Route::has('register'))
                    <a
                        href="{{ route('register') }}"
                        class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-black transition hover:bg-white/90"
                    >
                        Get Started
                    </a>
                @endif
            @endauth

        </div>

    </div>

</nav>


{{-- Hero --}}
<main>

    <section class="relative flex min-h-screen items-center overflow-hidden">

        {{-- Background glow --}}
        <div class="hero-glow absolute left-1/2 top-1/2 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/[0.06] blur-3xl"></div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-6 pt-32 pb-20 lg:px-8">

            <div class="max-w-4xl">

                <p class="reveal text-sm font-medium uppercase tracking-[0.3em] text-white/40">
                    A better way to shop
                </p>

                <h1 class="reveal mt-6 text-5xl font-semibold leading-[0.95] tracking-tight sm:text-7xl lg:text-8xl">
                    Good products.
                    <span class="text-white/35">
                        Beautifully simple.
                    </span>
                </h1>

                <p class="reveal mt-8 max-w-2xl text-lg leading-8 text-white/55 sm:text-xl">
                    Discover products worth bringing into your everyday life.
                    A simple store built around thoughtful choices, smooth experiences,
                    and the things you actually want.
                </p>

                <div class="reveal mt-10 flex flex-wrap gap-4">

                    <a
                        href="{{ route('products.index') }}"
                        class="rounded-2xl bg-white px-6 py-3.5 text-sm font-semibold text-black transition duration-300 hover:scale-[1.03] hover:bg-white/90"
                    >
                        Explore Products
                    </a>

                    <a
                        href="#story"
                        class="glass rounded-2xl px-6 py-3.5 text-sm font-semibold text-white transition duration-300 hover:bg-white/15"
                    >
                        Our Story
                    </a>

                </div>

            </div>

            <div class="mt-24 flex items-center gap-4 text-sm text-white/35">
                <span class="h-px w-12 bg-white/20"></span>
                Scroll to explore
            </div>

        </div>

    </section>


    {{-- Story --}}
    <section id="story" class="relative bg-white text-black">

        <div class="mx-auto max-w-7xl px-6 py-32 lg:px-8">

            <div class="grid gap-20 lg:grid-cols-2 lg:items-center">

                <div class="reveal">

                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-black/35">
                        The beginning
                    </p>

                    <h2 class="mt-5 text-4xl font-semibold tracking-tight sm:text-6xl">
                        Shopping should feel effortless.
                    </h2>

                    <p class="mt-8 max-w-xl text-lg leading-8 text-black/55">
                        We built this store around a simple idea:
                        finding something you love shouldn't feel complicated.
                    </p>

                    <p class="mt-5 max-w-xl text-lg leading-8 text-black/55">
                        Everything from discovering a product to adding it
                        to your cart is designed to stay simple, clear,
                        and enjoyable.
                    </p>

                </div>

                <div class="story-card reveal overflow-hidden rounded-[2rem] bg-black">

                    <div class="flex aspect-square items-center justify-center">

                        <div class="story-image text-[11rem] font-bold tracking-tighter text-white/10 sm:text-[15rem]">
                            01
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- Discovery --}}
    <section class="relative overflow-hidden bg-[#111]">

        <div class="mx-auto max-w-7xl px-6 py-32 lg:px-8">

            <div class="reveal max-w-3xl">

                <p class="text-sm font-medium uppercase tracking-[0.3em] text-white/30">
                    Discovery
                </p>

                <h2 class="mt-5 text-4xl font-semibold tracking-tight sm:text-6xl">
                    Find something that feels right.
                </h2>

                <p class="mt-7 text-lg leading-8 text-white/45">
                    Browse our collection, explore categories, and discover
                    products selected to make everyday life a little better.
                </p>

            </div>


            <div class="mt-20 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                <div class="glass reveal rounded-[2rem] p-8">

                    <div class="text-sm text-white/30">
                        01
                    </div>

                    <h3 class="mt-16 text-2xl font-semibold">
                        Explore
                    </h3>

                    <p class="mt-4 leading-7 text-white/45">
                        Take your time. Browse products and discover
                        something new.
                    </p>

                </div>


                <div class="glass reveal rounded-[2rem] p-8">

                    <div class="text-sm text-white/30">
                        02
                    </div>

                    <h3 class="mt-16 text-2xl font-semibold">
                        Choose
                    </h3>

                    <p class="mt-4 leading-7 text-white/45">
                        Find something you genuinely want and add it
                        to your collection.
                    </p>

                </div>


                <div class="glass reveal rounded-[2rem] p-8 sm:col-span-2 lg:col-span-1">

                    <div class="text-sm text-white/30">
                        03
                    </div>

                    <h3 class="mt-16 text-2xl font-semibold">
                        Enjoy
                    </h3>

                    <p class="mt-4 leading-7 text-white/45">
                        A smooth checkout and simple order experience
                        from beginning to end.
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- Final CTA --}}
    <section class="relative overflow-hidden bg-white text-black">

        <div class="mx-auto max-w-7xl px-6 py-40 text-center lg:px-8">

            <div class="reveal mx-auto max-w-3xl">

                <p class="text-sm font-medium uppercase tracking-[0.3em] text-black/30">
                    Your next discovery
                </p>

                <h2 class="mt-6 text-5xl font-semibold tracking-tight sm:text-7xl">
                    Maybe it's waiting for you.
                </h2>

                <p class="mx-auto mt-7 max-w-xl text-lg leading-8 text-black/50">
                    Start exploring and see where the journey takes you.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="mt-10 inline-flex rounded-2xl bg-black px-7 py-4 text-sm font-semibold text-white transition duration-300 hover:scale-[1.03] hover:bg-black/90"
                >
                    Start Exploring
                </a>

            </div>

        </div>

    </section>

</main>


{{-- Footer --}}
<footer class="bg-[#080808] px-6 py-10 text-white lg:px-8">

    <div class="mx-auto flex max-w-7xl flex-col gap-4 border-t border-white/10 pt-8 sm:flex-row sm:items-center sm:justify-between">

        <p class="text-sm text-white/30">
            © {{ date('Y') }} {{ config('app.name', 'Store') }}
        </p>

        <a
            href="{{ route('products.index') }}"
            class="text-sm text-white/40 transition hover:text-white"
        >
            Browse Products
        </a>

    </div>

</footer>


{{-- Animations --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const revealElements = document.querySelectorAll('.reveal');

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.12
            }
        );

        revealElements.forEach((element) => {
            observer.observe(element);
        });


        const dot = document.querySelector('.cursor-dot');
        const ring = document.querySelector('.cursor-ring');

        if (dot && ring) {

            let mouseX = 0;
            let mouseY = 0;
            let ringX = 0;
            let ringY = 0;

            document.addEventListener('mousemove', (event) => {

                mouseX = event.clientX;
                mouseY = event.clientY;

                dot.style.left = `${mouseX}px`;
                dot.style.top = `${mouseY}px`;

            });

            const animateCursor = () => {

                ringX += (mouseX - ringX) * 0.15;
                ringY += (mouseY - ringY) * 0.15;

                ring.style.left = `${ringX}px`;
                ring.style.top = `${ringY}px`;

                requestAnimationFrame(animateCursor);
            };

            animateCursor();


            document.querySelectorAll('a, button').forEach((element) => {

                element.addEventListener('mouseenter', () => {
                    ring.classList.add('active');
                });

                element.addEventListener('mouseleave', () => {
                    ring.classList.remove('active');
                });

            });

        }

    });
</script>

</body>
</html>
