<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-5xl mx-auto px-6 py-10">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">

            <div>

                <h1 class="text-3xl font-bold">
                    Create Campaign
                </h1>

                <p class="text-gray-600 mt-1">
                    Configure bulk email delivery settings.
                </p>

            </div>

            <a href="{{ route('campaigns.index') }}"
                class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-3 rounded-lg">
                Back
            </a>

        </div>

        <!-- Card -->
        <div class="bg-white shadow rounded-xl p-8">

            @if ($errors->any())

                <div class="bg-red-100 text-red-700 p-4 rounded mb-6">

                    <ul class="list-disc ml-5">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif

            <form action="{{ route('campaigns.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Campaign Name -->
                <div>

                    <label class="block mb-2 font-medium">
                        Campaign Name
                    </label>

                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-lg px-4 py-3" placeholder="Diwali Blast 2026"
                        required>

                </div>

                <!-- Template -->
                <div>

                    <label class="block mb-2 font-medium">
                        Select Template
                    </label>

                    <select name="template_id" class="w-full border rounded-lg px-4 py-3" required>

                        <option value="">
                            Select Template
                        </option>

                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

                <!-- Recipients -->
                <div class="bg-gray-50 border rounded-lg p-5">

                    <h2 class="font-semibold mb-4">
                        Recipient Settings
                    </h2>

                    <label class="flex items-center gap-3">

                        <input type="checkbox" name="send_to_all" value="1" checked>

                        <span>
                            Send to all active contacts
                        </span>

                    </label>

                </div>

                <!-- Schedule -->
                <div>

                    <label class="block mb-2 font-medium">
                        Schedule Delivery (Optional)
                    </label>

                    <input type="datetime-local" name="scheduled_at"
                        class="w-full border rounded-lg px-4 py-3">

                </div>

                <!-- Delivery Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">

                    <h2 class="font-semibold text-blue-700 mb-3">
                        Delivery Workflow
                    </h2>

                    <ul class="list-disc ml-5 text-sm text-gray-700 space-y-2">

                        <li>
                            Campaign recipients will be generated automatically.
                        </li>

                        <li>
                            Emails will be processed asynchronously via queues.
                        </li>

                        <li>
                            Failed emails can be retried later.
                        </li>

                        <li>
                            Delivery statuses are tracked individually.
                        </li>

                    </ul>

                </div>

                <!-- Submit -->
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg">
                    Create Campaign
                </button>

            </form>

        </div>

    </div>

</body>

</html>
