<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Upload Contacts</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-2xl mx-auto py-16 px-6">

        <div class="bg-white rounded-xl shadow-md p-8">

            <h1 class="text-3xl font-bold mb-2">
                Upload Contacts CSV
            </h1>

            <p class="text-gray-600 mb-8">
                Upload CSV or Excel file containing contacts.
            </p>

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contacts.import') }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block mb-2 font-medium">
                        Select File
                    </label>

                    <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                        class="w-full border rounded-lg p-3">
                </div>

                <div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                        Upload Contacts
                    </button>
                </div>
            </form>

            <div class="mt-10 border-t pt-6">

                <h2 class="font-semibold mb-3">
                    Expected CSV Format
                </h2>

                <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto">email,first_name,last_name,company,city
john@test.com,John,Doe,Google,Delhi
jane@test.com,Jane,Smith,Amazon,Mumbai</pre>

            </div>

        </div>

    </div>

</body>

</html>
