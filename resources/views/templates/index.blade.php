<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-10">

        <div class="flex justify-between items-center mb-8">

            <div>

                <h1 class="text-3xl font-bold">
                    Email Templates
                </h1>

                <p class="text-gray-600 mt-1">
                    Manage reusable email templates.
                </p>

            </div>

            <div class="flex gap-3">

                <a href="/"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-3 rounded-lg">
                    Dashboard
                </a>

                <a href="{{ route('templates.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg">
                    Create Template
                </a>

            </div>

        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-xl overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-50 border-b">

                    <tr>

                        <th class="text-left px-6 py-4">ID</th>

                        <th class="text-left px-6 py-4">
                            Template Name
                        </th>

                        <th class="text-left px-6 py-4">
                            Subject
                        </th>

                        <th class="text-left px-6 py-4">
                            Created
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($templates as $template)
                        <tr class="border-b hover:bg-gray-50">

                            <td class="px-6 py-4">
                                {{ $template->id }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $template->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $template->subject }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $template->created_at->format('d M Y') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center py-10 text-gray-500">

                                No templates found.

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-6">
            {{ $templates->links() }}
        </div>

    </div>

</body>

</html>
