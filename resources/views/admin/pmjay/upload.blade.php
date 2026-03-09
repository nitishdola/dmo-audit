<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload PMJAY JSON</title>
</head>
<body>
  <h2>Upload PMJAY JSON</h2>

  @if(session('success'))
  <p>{{ session('success') }}</p>
  @endif

  <form method="POST" action="{{ route('admin.pmjay.import') }}" enctype="multipart/form-data">

  @csrf

  <p>Select JSON file</p>

  <input type="file" name="file" required>

  <br><br>

  <button type="submit">Import Data</button>

  </form>
</body>
</html>