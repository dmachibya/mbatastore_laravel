@if (config('app.demo') == true)
  <div class="text-left mt-3 ml-4">
    <h4>Demo Customer:: <button class="btn btn-primary btn-sm" id="customer-demo">Login</button></h4>
    <p class="my-2">Username: <strong>customer@demo.com</strong> | Password: <strong>123456</strong></p>
  </div>

  <script>
    document.getElementById('customer-demo').addEventListener("click", function() {
      document.getElementById('email').value = 'customer@demo.com';
      document.getElementById('password').value = '123456';
      document.getElementById('loginForm-1').submit();
    });
  </script>
@endif
