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
            <td>{{ $import->import_date }} <p>
                    {{ $import->created_at->diffForHumans(); }}
                </p>
            </td>
            <td>{{ $import->file }}</td>
            <td>{{ $import->status }}</td>
        </tr>
        @endforeach

    </tbody>
</table>
