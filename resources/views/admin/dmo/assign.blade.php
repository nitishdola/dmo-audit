<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <h2>Assign Districts to {{ $dmo->name }}</h2>

<form method="POST" action="{{ route('admin.dmo.districts.store',$dmo->id) }}">
@csrf

@foreach($districts as $district)

<label>
<input type="checkbox"
name="districts[]"
value="{{ $district->id }}"
{{ in_array($district->id,$assigned) ? 'checked' : '' }}
>

{{ $district->name }}
</label>

<br>

@endforeach

<button type="submit">Save</button>

</form>
</body>
</html>