<!DOCTYPE html>
<html>
<head>
    <meta content="HTML Tidy for HTML5 for Windows version 5.2.0" name="generator">
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="VERACiTY | PLM Payroll System">
    <meta name="author" content="MiSAKACHi">
    <title>
        @yield( 'pageTitle' ) | VERACiTY
    </title>
    <link href="{{ config('app.root') }}/files/img/common/plm_seal_2014.png" rel="icon" type="image/x-icon">
    <link crossorigin="anonymous" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" integrity="sha384-gOaRlqAhqPUMlR/5HfjaLm+COAJ+Ka0Am9GCueJAWwFluNWKDUZJ8GUGhBJ1r+J/" rel="stylesheet">
    <link href="{{ config('app.root') }}/files/css/AdminLTE.min.css" rel="stylesheet">
    @yield( 'additionalCss' )
</head>
<body>
<div class="wrapper">
    @yield( 'pageContent' )
</div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
<script>
    $.widget.bridge( 'uibutton', $.ui.button );
</script>
<script crossorigin="anonymous" defer integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@yield( 'additionalJs' )
</body>
</html>
