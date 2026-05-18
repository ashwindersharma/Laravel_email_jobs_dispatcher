<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-10">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Contacts
                </h1>

                <p class="text-gray-600 mt-1">
                    Manage all imported contacts.
                </p>
            </div>

            <div class="flex gap-3">

                <a href="/"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-lg">
                    Dashboard
                </a>

                <a href="/contacts/upload"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg">
                    Upload Contacts
                </a>

            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">

            <form method="GET" action="/contacts">

                <div class="flex gap-4">

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by email or name..."
                        class="w-full border rounded-lg px-4 py-3">

                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 rounded-lg">
                        Search
                    </button>

                </div>

            </form>

        </div>

        <!-- Contacts Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50 border-b">

                        <tr>

                            <th class="text-left px-6 py-4 font-semibold text-gray-700">
                                ID
                            </th>

                            <th class="text-left px-6 py-4 font-semibold text-gray-700">
                                Email
                            </th>

                            <th class="text-left px-6 py-4 font-semibold text-gray-700">
                                First Name
                            </th>

                            <th class="text-left px-6 py-4 font-semibold text-gray-700">
                                Last Name
                            </th>

                            <th class="text-left px-6 py-4 font-semibold text-gray-700">
                                Status
                            </th>

                            <th class="text-left px-6 py-4 font-semibold text-gray-700">
                                Created
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($contacts as $contact)
                            <tr class="border-b hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    {{ $contact->id }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $contact->email }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $contact->first_name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $contact->last_name }}
                                </td>

                                <td class="px-6 py-4">

                                    @if ($contact->status == 'active')
                                        <span
                                            class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                            Inactive
                                        </span>
                                    @endif

                                </td>

                                <td class="px-6 py-4">
                                    {{ $contact->created_at->format('d M Y') }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center py-10 text-gray-500">

                                    No contacts found.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $contacts->links() }}
        </div>

    </div>

</body>

</html>
