<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi PIN - E-Supervisi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Sesi Terkunci</h1>
            <p class="text-gray-600 mt-2">Masukkan PIN untuk melanjutkan</p>
        </div>

        <form method="POST" action="{{ route('pin.verify.post') }}">
            @csrf
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    {{ $errors->first('pin') }}
                </div>
            @endif

            <div class="mb-6">
                <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN Keamanan</label>
                <input 
                    type="password" 
                    id="pin" 
                    name="pin" 
                    maxlength="6"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-2xl tracking-widest"
                    placeholder="••••••"
                    required
                    autofocus
                >
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200"
            >
                Buka Sesi
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sesi akan terkunci setelah {{ config('session.timeout_minutes', 15) }} menit tidak aktif
        </p>
    </div>
</body>
</html>
