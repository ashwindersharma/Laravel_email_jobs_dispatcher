<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Email Management Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-10">

        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-800">
                Bulk Email Management Portal
            </h1>

            <p class="text-gray-600 mt-2">
                Manage contacts, templates, campaigns, and bulk email workflows.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Upload Contacts -->
            <a href="/contacts/upload"
                class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 border">

                <div class="text-2xl mb-4">
                    📤
                </div>

                <h2 class="text-xl font-semibold mb-2">
                    Upload Contacts
                </h2>

                <p class="text-gray-600">
                    Import contacts using CSV or Excel files.
                </p>
            </a>

            <!-- Contacts -->
            <a href="/contacts"
                class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 border">

                <div class="text-2xl mb-4">
                    👥
                </div>

                <h2 class="text-xl font-semibold mb-2">
                    Manage Contacts
                </h2>

                <p class="text-gray-600">
                    View and manage imported contacts.
                </p>
            </a>

            <!-- Templates -->
            <a href="/templates"
                class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 border">

                <div class="text-2xl mb-4">
                    📝
                </div>

                <h2 class="text-xl font-semibold mb-2">
                    Email Templates
                </h2>

                <p class="text-gray-600">
                    Create and manage reusable email templates.
                </p>
            </a>

            <!-- Campaigns -->
            <a href="/campaigns"
                class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 border">

                <div class="text-2xl mb-4">
                    📧
                </div>

                <h2 class="text-xl font-semibold mb-2">
                    Campaigns
                </h2>

                <p class="text-gray-600">
                    Create, schedule, and send campaigns.
                </p>
            </a>

            <!-- Queue Monitor -->
            <a href="/jobs"
                class="bg-white rounded-xl shadow hover:shadow-lg transition p-6 border">

                <div class="text-2xl mb-4">
                    ⚙️
                </div>

                <h2 class="text-xl font-semibold mb-2">
                    Queue Monitor
                </h2>

                <p class="text-gray-600">
                    Monitor jobs, retries, and failures.
                </p>
            </a>

            <!-- Analytics -->






        </div>

    </div>

</body>

</html>
