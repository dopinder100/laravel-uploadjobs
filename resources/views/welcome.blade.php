<!DOCTYPE html>
<html lang="en">
<head>
  <title>Upload File and Background Process</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Upload File</h2>

    @if(session()->has('success'))
    <div class="alert alert-success"> {{ session()->get('success') }} </div>
    @endif

    <form method="POST" action="{{ route('product.upload') }}" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-group">
            <label>Attach File</label>
            <input name="file" type="file" class="form-control" />
        </div>
        <input type="submit" value="Upload File" class="btn btn-primary" />
    </form>

    <br><br>

    <div id="result">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>File</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($imports as $import)
                <tr>
                    <td>{{ $import->import_date }} <p>{{ $import->created_at->diffForHumans(); }}</p></td>
                    <td>{{ $import->file }}</td>
                    <td>{{ $import->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


</body>

<script>
    setInterval(check_status, 2000);
    function check_status()
    {
        $.ajax({
            url:"{{ route('upload.status') }}",
            type:"GET",
            success:function(data)
            {
                $("#result").html(data);
            }

        });
    }
</script>

</html>
