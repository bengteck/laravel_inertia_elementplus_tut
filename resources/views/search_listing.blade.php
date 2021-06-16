<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @include('section.menu')
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
    <div>
        <form method="get" >
            <label for="parent">Parent</label>
            <select id="parent" name="parent">
                @foreach ($parents as $parent)
                    @if( isset($selection['parent']) && $selection['parent'] == $parent )
                    <option value="{{ $parent }}" selected>{{ $parent }}</option>
                    @else
                    <option value="{{ $parent }}" >{{ $parent }}</option>
                    @endif
                @endforeach
            </select>
            <label for="from_date">From</label>
            <input type="date" name="from_date" id="from_date"
                value="{{ isset($selection['from_date']) ? $selection['from_date'] : '' }}" >
            <label for="to_date">To</label>
            <input type="date" name="to_date" id="to_date"
                value="{{ isset($selection['to_date']) ? $selection['to_date'] : '' }}" >
            <button type="submit">Search</button>
            <button type="button" onclick="window.location = window.location.pathname;return false">Reset</button>
            <button type="button" onclick="dd(); return false">copy</button>
        </form>
        <script>
            function dd() {
                const table = document.querySelector('table')
                const range = document.createRange();
                range.selectNode(table)
                window.getSelection().addRange(range);
                document.execCommand("copy")
                window.getSelection().removeAllRanges();
                return false;
            }
        </script>
    </div>
    <div>
        last: {{ $dates['last'] }}
        selection:
        @if ( isset($selection) )
            {{ json_encode($selection) }}
        @else
            -
        @endif
    </div>
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