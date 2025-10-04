<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title') - {{ setting('app_name') }}</title>

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ url('assets/img/icons/apple-touch-icon-144x144.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ url('assets/img/icons/apple-touch-icon-152x152.png') }}" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-16x16.png') }}" sizes="16x16" />
    <meta name="application-name" content="{{ setting('app_name') }}"/>
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="{{ url('assets/img/icons/mstile-144x144.png') }}" />

    <link media="all" type="text/css" rel="stylesheet" href="{{ url(mix('assets/css/vendor.css')) }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ url(mix('assets/css/app.css')) }}">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @yield('styles')

    @hook('app:styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            @yield('content')
        </div>
    </div>

    <script src="{{ url(mix('assets/js/vendor.js')) }}"></script>
    <script src="{{ url('assets/js/as/app.js') }}"></script>
    @yield('scripts')

    @hook('app:scripts')

    <!-- Script to remove special characters from text inputs and textareas on focusout -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const disableCharacters = @json(config('setting.disable_characters'));

            // Function to remove special characters from a string
            function removeSpecialCharacters(str) {
                if (!disableCharacters || disableCharacters.length === 0) {
                    return str;
                }

                // Create a regex pattern to match any of the disabled characters
                const pattern = new RegExp('[' + disableCharacters.map(char =>
                    // Escape special regex characters
                    char.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
                ).join('') + ']', 'g');

                return str.replace(pattern, '');
            }

            // Handle focusout event for text inputs and textareas
            document.addEventListener('focusout', function(e) {
                const target = e.target;

                // Check if the element is a text input or textarea
                if ((target.tagName === 'INPUT' && target.type === 'text') ||
                    target.tagName === 'TEXTAREA') {

                    // Remove special characters and update the value
                    if (target.value) {
                        const originalValue = target.value;
                        const cleanedValue = removeSpecialCharacters(originalValue);

                        if (originalValue !== cleanedValue) {
                            target.value = cleanedValue;

                            // Trigger input event to notify any listeners
                            target.dispatchEvent(new Event('input', { bubbles: true }));

                            // Show a notification if available (optional)
                            if (window.toast) {
                                toast.warning('{{ __("app.special_characters_removed") }}.');
                            }
                        }
                    }
                }
            });

            // Also handle form submission to clean all fields
            document.addEventListener('submit', function(e) {
                const textInputs = document.querySelectorAll('input[type="text"]');
                const textareas = document.querySelectorAll('textarea');

                [...textInputs, ...textareas].forEach(field => {
                    if (field.value) {
                        const originalValue = field.value;
                        const cleanedValue = removeSpecialCharacters(originalValue);

                        if (originalValue !== cleanedValue) {
                            field.value = cleanedValue;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
