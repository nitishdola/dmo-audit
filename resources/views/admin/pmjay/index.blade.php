<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h2>PMJAY Treatment Records</h2>

<a href="{{ route('admin.pmjay.upload') }}">Upload JSON</a>

@if(session('success'))
<p>{{ session('success') }}</p>
@endif

<table border="1" cellpadding="5">

<tr>
    <th>Registration</th>
    <th>Patient</th>
    <th>Hospital</th>
    <th>District</th>
    <th>Procedure</th>
    <th>Status</th>
</tr>

@foreach($records as $r)

<tr>

<td>{{ $r->registration_id }}</td>

<td>{{ $r->patient_name }}</td>

<td>{{ $r->hospital->name ?? '' }}</td>

<td>{{ $r->hospital->district->name ?? '' }}</td>

<td>{{ $r->procedure_details }}</td>

<td>{{ $r->case_status }}</td>

</tr>

@endforeach

</table>

<br>

{{ $records->links() }}
</body>
</html>