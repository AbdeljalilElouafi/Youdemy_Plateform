<!DOCTYPE html> <html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Youdemy - Authentication</title> <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet"> <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <style> body { background: linear-gradient(135deg, #6b5b95, #feb236); } .card { background: rgba(255, 255, 255, 0.9); border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); } .button { transition: transform 0.2s; } .button:hover { transform: scale(1.05); } .fade-in { animation: fadeIn 1.5s ease-in-out; } @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } } </style> </head> <body class="flex items-center justify-center min-h-screen"> <div class="card p-8 sm:w-full sm:max-w-md fade-in"> <div class="flex justify-center mb-6"> <h1 class="text-4xl font-extrabold text-indigo-600">Youdemy</h1> </div> <div class="flex justify-center space-x-4 mb-6"> <button onclick="showLogin()" id="loginTab" class="text-lg font-semibold text-indigo-600 pb-2 border-b-2 border-indigo-600 transition duration-300 hover:text-indigo-500">Sign In</button> <button onclick="showRegister()" id="registerTab" class="text-lg font-semibold text-gray-500 pb-2 border-b-2 border-transparent transition duration-300 hover:text-indigo-600">Register</button> </div>


    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 fade-in">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <div id="loginForm" class="mt-10 fade-in">
        <form class="space-y-6" action="#" method="POST">
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-900">Email address</label>
                <input type="email" name="email" required 
                       class="block w-full rounded-lg px-4 py-3 bg-gray-100 border border-gray-300 text-gray-900 
                              focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition duration-200">
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
                    <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition duration-200">Forgot password?</a>
                </div>
                <input type="password" name="password" required 
                       class="block w-full rounded-lg px-4 py-3 bg-gray-100 border border-gray-300 text-gray-900 
                              focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition duration-200">
            </div>

            <div>
                <button type="submit" 
                        class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm 
                               button focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    Sign in
                </button>
            </div>

            <!-- <div>
                <a href="../View/landing-page.php"
                   class="w-full rounded-lg bg-black px-4 py-3 text-sm font-semibold text-white shadow-sm 
                          button focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    Return
                </a>
            </div> -->
        </form>
    </div>

    <!-- Register Form -->
    <div id="registerForm" class="hidden mt-10 fade-in">
        <form class="space-y-6" action="#" method="POST">
            <div class="space-y-2">
                <label for ="first-name" class="block text-sm font-medium text-gray-900">First Name</label>
                <input type="text" name="first-name" required 
                       class="block w-full rounded-lg px-4 py-3 bg-gray-100 border border-gray-300 text-gray-900 
                              focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition duration-200">
            </div>
            <div class="space-y-2">
                <label for="last-name" class="block text-sm font-medium text-gray-900">Last Name</label>
                <input type="text" name="last-name" required 
                       class="block w-full rounded-lg px-4 py-3 bg-gray-100 border border-gray-300 text-gray-900 
                              focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition duration-200">
            </div>

            <div class="space-y-2">
                <label for="reg-email" class="block text-sm font-medium text-gray-900">Email address</label>
                <input type="email" name="reg-email" required 
                       class="block w-full rounded-lg px-4 py-3 bg-gray-100 border border-gray-300 text-gray-900 
                              focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition duration-200">
            </div>

            <div class="space-y-2">
                <label for="reg-password" class="block text-sm font-medium text-gray-900">Password</label>
                <input type="password" name="reg-password" required 
                       class="block w-full rounded-lg px-4 py-3 bg-gray-100 border border-gray-300 text-gray-900 
                              focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition duration-200">
            </div>

            <div class="space-y-2">
                <label for="role" class="block text-sm font-medium text-gray-900">Role</label>
                <select name="role" required 
                        class="block w-full rounded-lg px-4 py-3 bg-gray-100 border border-gray-300 text-gray-900 
                               focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition duration-200">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
            </div>

            <div>
                <button type="submit" 
                        class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm 
                               button focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    Create Account
                </button>
            </div>
            <div>
                <a href="../View/landing-page.php"
                   class="w-full rounded-lg bg-black px-4 py-3 text-sm font-semibold text-white shadow-sm 
                          button focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                    Return
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function showLogin() {
        document.getElementById('loginForm').classList.remove('hidden');
        document.getElementById('registerForm').classList.add('hidden');
        document.getElementById('loginTab').classList.add('text-indigo-600', 'border-indigo-600');
        document.getElementById('loginTab').classList.remove('text-gray-500', 'border-transparent');
        document.getElementById('registerTab').classList.add('text-gray-500', 'border-transparent');
        document.getElementById('registerTab').classList.remove('text-indigo-600', 'border-indigo-600');
    }

    function showRegister() {
        document.getElementById('registerForm').classList.remove('hidden');
        document.getElementById('loginForm').classList.add('hidden');
        document.getElementById('registerTab').classList.add('text-indigo-600', 'border-indigo-600');
        document.getElementById('registerTab').classList.remove('text-gray-500', 'border-transparent');
        document.getElementById('loginTab').classList.add('text-gray-500', 'border-transparent');
        document.getElementById('loginTab').classList.remove('text-indigo-600', 'border-indigo-600');
    }
</script>
</body> </html>