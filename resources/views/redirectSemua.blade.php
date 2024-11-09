<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <form id="redirectForm" action="{{ route('Evaluasisemua') }}" method="POST">
        @csrf
        <input type="hidden" name="slug" value="{{ $slug }}">
        <input type="hidden" name="ujian_id" value="{{ $id_ujian }}">
    </form>
    <script type="text/javascript">
        document.getElementById('redirectForm').submit();
    </script>
</body>
</html>