<!DOCTYPE html>
<html>

<head>
  <title>{{ trans('messages.permission.denied') }}</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

  <style>
    .content {
      display: flex;
      position: relative;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      top: 135px;
    }

    .title {
      font-size: 21px;
      margin-top: 20px;
      margin-bottom: 40px;
    }

    .brand-logo {
      max-width: 140px;
      max-height: 50px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="content">
      <div class="title">{{ trans('messages.permission.denied') }}</div>
      <a href="{{ route('admin.admin.dashboard') }}" class="btn btn-default">::Back to dashboard::</a>
    </div>
  </div>
</body>

</html>
