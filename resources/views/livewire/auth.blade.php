<div>
  <div
    id="auth_modal"
    class="fixed hidden top-0 left-0 z-130 w-screen min-h-screen bg-gray-100 flex items-center justify-center p-2 md:!p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg !p-4 md:!p-8 relative">
      <div class="close_auth absolute top-8 right-8 rotate-45 stroke-gray-400 transition hover:cursor-pointer hover:!stroke-black z-20">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
          <path d="M8.19982 2.84314C8.19982 7.26142 8.19982 9.73857 8.19982 14.1568M2.54297 8.49999C6.96125 8.49999 9.4384 8.49999 13.8567 8.49999" stroke="inherit" stroke-width="1.5" stroke-linecap="round"/>
          <script xmlns=""/>
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Sign In</h2>
      
      <form method="POST" action="/auth/signin" class="!space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input 
            type="email" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all"
            placeholder="your@email.com"
            name="email"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input 
            type="password" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all"
            placeholder="••••••••"
            name="password"
          />
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center">
            <input type="checkbox" name="remember" class="!rounded !border-gray-300 !text-orange-600 !focus:ring-orange-500 checked:bg-orange-500"/>
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
          </label>
          <a href="#" class="text-sm !text-orange-400 hover:!text-orange-600 transition">Forgot password?</a>
        </div>

        <button class="w-full !bg-orange-400 hover:!bg-orange-600 text-white font-medium !py-2.5 !rounded-lg transition-colors transition">
          Sign In
        </button>
      </form>

      <div class="!mt-6 text-center text-sm text-gray-600">
        Don't have an account? 
        <a href="#" class="!text-orange-400 hover:!text-orange-600 font-medium transition">Sign up</a>
      </div>
    </div>
  </div>
</div>
