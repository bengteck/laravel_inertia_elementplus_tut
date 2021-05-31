<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>
        td {
            white-space: nowrap;
        }
    </style>
    @php
        $keys = ['nothing'];
        if ( $rows && count($rows) > 0 ) {
            if ( is_array($rows) ) {
                // for array results
                $keys = array_keys($rows[0]);
            } else {
                // for collection results
                $keys = array_keys($rows->get(0)->toArray());
            }
        }
    @endphp
    <h3>Total result {{ count($rows) }}</h3>
    <table>
        <tr>
            @foreach ($keys as $key)
            <th>{{ $key }}</td>    
            @endforeach
        </tr>
        @foreach ($rows as $row)
        <tr>
            @foreach ($keys as $key)
                @if(is_array($row))
                <td>{{ $row[$key] }}</td>    
                @else
                <td>{{ $row->$key }}</td>    
                @endif
            @endforeach
        </tr>
        @endforeach
    </table>
</body>
</html>