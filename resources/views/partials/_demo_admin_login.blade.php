@if (config('app.demo') == true)
  <hr />
  <div class="row ml-4">
    <div class="col-md-6">
      <h5>ADMIN:: <button class="btn btn-new btn-sm" id="admin-demo">Login</button></h5>
      Username: <strong>superadmin@demo.com</strong> <br /> Password: <strong>123456</strong>
    </div>
    <div class="col-md-6">
      <h5>MERCHANT:: <button class="btn btn-new btn-sm" id="merhcant-demo">Login</button></h5>
      Username: <strong>merchant@demo.com</strong> <br /> Password: <strong>123456</strong>
    </div>
  </div>
  <div class="spacer20"></div>

  <script>
    document.getElementById('admin-demo').addEventListener("click", function() {
      document.getElementById('email').value = 'superadmin@demo.com';
      document.getElementById('password').value = '123456';
      document.forms[0].submit();
    });

    document.getElementById('merhcant-demo').addEventListener("click", function() {
      document.getElementById('email').value = 'merchant@demo.com';
      document.getElementById('password').value = '123456';
      document.forms[0].submit();
    });
  </script>
@endif
