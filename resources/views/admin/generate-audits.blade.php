<!DOCTYPE html>
<html>
<head>
    <title>Generate Audits</title>
</head>
<body>

<h2>Generate PMJAY Audit Cases</h2>

@if(session('success'))
<p style="color:green;">{{ session('success') }}</p>
@endif

@if(session('error'))
<p style="color:red;">{{ session('error') }}</p>
@endif

@if($alreadyGenerated)

<p>Audits have already been generated.</p>

@else

<form method="POST" action="/admin/generate-audits">
    @csrf

    <button type="submit">
        Generate Audits
    </button>

</form>

@endif

</body>
</html>