<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Template</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto px-6 py-10">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">

            <div>
                <h1 class="text-3xl font-bold">
                    Create Email Template
                </h1>

                <p class="text-gray-600 mt-1">
                    Build reusable templates for campaigns.
                </p>
            </div>

            <a href="{{ route('templates.index') }}"
                class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-3 rounded-lg">
                Back
            </a>

        </div>

        <!-- Form Card -->
        <div class="bg-white shadow rounded-xl p-8">

            <!-- Validation Errors -->
            @if ($errors->any())

                <div class="bg-red-100 text-red-700 p-4 rounded mb-6">

                    <ul class="list-disc ml-5">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            <!-- Form -->
            <form action="{{ route('templates.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Template Name -->
                <div>

                    <label class="block mb-2 font-medium">
                        Template Name
                    </label>

                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-4 py-3" placeholder="Welcome Email"
                        required>

                </div>

                <!-- Subject -->
                <div>

                    <label class="block mb-2 font-medium">
                        Email Subject
                    </label>

                    <input type="text" name="subject" value="{{ old('subject') }}"
                        class="w-full border rounded-lg px-4 py-3"
                        placeholder="Welcome {{ '{{first_name ?>' }}' }}" required>

                </div>

                <!-- Email Body -->
                <div>

                    <label class="block mb-2 font-medium">
                        Email Body
                    </label>

                    <textarea name="body" rows="14" class="w-full border rounded-lg px-4 py-3"
                        placeholder="Hello {{ '{{first_name ?>' }}' }}, Welcome to {{ '{{company ?>' }}' }}" required>{{ old('body') }}</textarea>

                </div>

                <!-- Placeholder Guide -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">

                    <h2 class="font-semibold mb-3 text-blue-700">
                        Available Placeholders
                    </h2>

                    <div class="grid grid-cols-2 gap-3 text-sm">

                        <div>{{ '{{first_name ?>' }}' }}</div>

                        <div>{{ '{{last_name ?>' }}' }}</div>

                        <div>{{ '{{email ?>' }}' }}</div>

                        <div>{{ '{{company ?>' }}' }}</div>

                        <div>{{ '{{city ?>' }}' }}</div>

                    </div>

                </div>

                <!-- Submit -->
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg">
                    Save Template
                </button>

            </form>

        </div>

    </div>

</body>

</html>
