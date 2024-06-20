@foreach ($record as $r)
    <tr>
        <th scope="row">{{ $r->id }}</th>
        <td>{{ $r->text }}</td>
        <td>{{ $r->created_at }}</td>
    </tr>
@endforeach
