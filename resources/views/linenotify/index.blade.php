<!DOCTYPE html>
<html lang="tw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web | Index</title>

    <link rel="stylesheet" href="/front/css/bootstrap.css">
    <script src="/front/js/bootstrap.js"></script>
    <script defer src="/front/js/jquery-3.7.1.min.js"></script>
    <script defer src="/backend/index.js"></script>
</head>

<body>

    <div class="container mb-3">
        <div>
            <h1 class="text-center mb-3">client id :{{ $result['client_id'] }}</h1>
            <input type="hidden" name="_data" data-json="{{ json_encode($result) }}" />
            <div class="input-group mb-3">
                <button class="btn btn-outline-secondary" type="button" id="btn">發送訊息</button>
                <input type="text" class="form-control" placeholder="" aria-label="text" aria-describedby="btn"
                    name="text" id="text">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            </div>
        </div>
        <div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">發送訊息</th>
                        <th scope="col">時間</th>
                    </tr>
                </thead>
                <tbody class="table-data">
                    @if (count($record) > 0)
                        @foreach ($record as $r)
                            <tr>
                                <th scope="row">{{ $r->id }}</th>
                                <td>{{ $r->text }}</td>
                                <td>{{ $r->created_at }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
