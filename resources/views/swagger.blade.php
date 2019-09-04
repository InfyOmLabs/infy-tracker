<html>
<head>
    <title>{{ config('app.name') }} Swagger</title>
    <link href="{{ asset('css/swagger-ui.css') }}" rel="stylesheet"></head>
</head>
<body>
<div id="swagger-ui"></div>
</body>
<script src="//unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
<script type="application/javascript">
    const ui = SwaggerUIBundle({
        url: "{{ asset('swagger/swagger.yaml') }}",
        dom_id: '#swagger-ui'
    })
</script>
</html>
