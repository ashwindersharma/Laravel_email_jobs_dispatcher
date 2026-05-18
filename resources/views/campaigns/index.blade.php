<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaigns</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-10">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">

            <div>

                <h1 class="text-3xl font-bold">
                    Campaigns
                </h1>

                <p class="text-gray-600 mt-1">
                    Manage bulk email campaigns.
                </p>

            </div>

            <div class="flex gap-3">

                <a href="/"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-3 rounded-lg">
                    Dashboard
                </a>

                <a href="{{ route('campaigns.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg">
                    Create Campaign
                </a>

            </div>

        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white shadow rounded-xl overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-50 border-b">

                    <tr>

                        <th class="text-left px-6 py-4">
                            ID
                        </th>

                        <th class="text-left px-6 py-4">
                            Campaign
                        </th>

                        <th class="text-left px-6 py-4">
                            Template
                        </th>

                        <th class="text-left px-6 py-4">
                            Status
                        </th>

                        <th class="text-left px-6 py-4">
                            Scheduled
                        </th>

                        <th class="text-left px-6 py-4">
                            Created
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($campaigns as $campaign)
                        <tr class="border-b hover:bg-gray-50">

                            <td class="px-6 py-4">
                                {{ $campaign->id }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $campaign->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $campaign->template->name }}
                            </td>

                            <td class="px-6 py-4">

                                <span
                                    class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">

                                    {{ ucfirst($campaign->status) }}

                                </span>

                            </td>

                            <td class="px-6 py-4">

                                {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('d M Y H:i') : '-' }}

                            </td>

                            <td class="px-6 py-4">
                                {{ $campaign->created_at->format('d M Y') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center py-10 text-gray-500">

                                No campaigns found.

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-6">
            {{ $campaigns->links() }}
        </div>

    </div>

</body>

</html>
