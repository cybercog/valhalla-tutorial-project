<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Valhalla</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="padding-top: 100px">

<div class="container">
    @if(isset($invalid))
        <div class="alert alert-danger text-center">
            Invalid authorization request.
        </div>
    @else

        @if(session('message'))
            <div class="alert alert-danger text-center">
                {{session('message')}}
            </div>
        @endif

        <form class="form-horizontal" role="form" method="POST" action="{{ url('/authorize') }}">
            {!! csrf_field() !!}
            <input type="hidden" name="app_key" value="{{ request('app_key') }}">
            <input type="hidden" name="redirect_uri" value="{{ request('redirect_uri') }}">

            <h1 class="text-center" style="margin-bottom: 70px">Authorise "{{$app->name}}" to use your data</h1>

            <div class="form-group">
                <label class="col-md-4 control-label">E-Mail Address</label>

                <div class="col-md-6">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label">Password</label>

                <div class="col-md-6">
                    <input type="password" class="form-control" name="password">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-success">
                        Authorise
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>

</body>
</html>