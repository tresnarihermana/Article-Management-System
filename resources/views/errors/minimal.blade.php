<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('code') - @yield('message')</title>
    <link rel="icon" href="storage/img/logo.svg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white">
    @php
        $lotties = [
            403 => 'https://d1jj76g3lut4fe.cloudfront.net/saved_colors/122657/XLrhDUmWewxm4Eft.json',
            404 => 'https://d1jj76g3lut4fe.cloudfront.net/saved_colors/122657/KQOydl2r8hMISe8Z.json',
            429 => 'https://d1jj76g3lut4fe.cloudfront.net/saved_colors/Y9unX921c8bp2WN698.json',
            500 => 'https://d1jj76g3lut4fe.cloudfront.net/saved_colors/122657/x9DT7OF0YAdBiZ0c.json',
        ];
        $defaultLottie = 'https://d1jj76g3lut4fe.cloudfront.net/saved_colors/122657/x9DT7OF0YAdBiZ0c.json';
        $errorCode = (int) trim($__env->yieldContent('code', 500));
        $errorColor = match ($errorCode) {
         404, 419, 429 => 'warning',
           403 ,500, 503 => 'danger',
            default => 'secondary',
        };
        $twTextColor = match ($errorColor) {
            'warning' => 'text-amber-500',
            'danger' => 'text-red-600',
            default => 'text-gray-500',
        };
        $errorAdditionalMessage = match ($errorCode) {
            401 => 'Unauthorized access. You need to log in to access this resource.',
            403 => 'It seems you do not have permission to access or perform this action.',
            404 => 'The page you are looking for could not be found on our server.',
            419 => 'The page has expired due to inactivity. Please refresh and try again.',
            429 => 'You have sent too many requests in a given amount of time. Please slow down and try again later.',
            503 => 'The server is currently unavailable (because it is overloaded or down for maintenance). Please try again later.',
            default => 'Yeah Something definitely went wrong here (Not sure Tho).',
        };
    @endphp

    <section class="min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-6 w-full">
            <div class="flex flex-col md:flex-row md:items-center gap-8">
                <div class="md:w-1/2 md:order-2 w-full flex justify-center">
                    <div class="max-w-[720px] w-full">
                        <creattie-embed
                            src="{{ $lotties[$errorCode] ?? $defaultLottie }}"
                            delay="1" speed="100" frame_rate="24" trigger="loop" class="w-full">
                        </creattie-embed>
                        <script src="https://creattie.com/js/embed.js?id=3f6954fde297cd31b441" defer></script>
                    </div>
                </div>

                <div class="md:w-1/2 md:order-1 w-full text-center md:text-left">
                    <div class="mb-6">
                        <h1 class="text-[72px] leading-tight font-extrabold {{ $twTextColor }}">Error @yield('code')</h1>
                        <h2 class="text-2xl font-bold {{ $twTextColor }} mt-2">@yield('message')</h2>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm font-light text-gray-600"> <span class="font-bold">{{ $errorAdditionalMessage }}</span> If you think there is some mistake, please contact the admin.</p>
                    </div>

                    <div>
                        <a href="{{ url('/') }}" role="button" class="inline-block px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white text-lg font-medium">Back to homepage</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
