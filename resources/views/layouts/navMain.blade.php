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
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" integrity="sha384-5NocS5vv1V7pNg+id43cLyH4VlyQHptfkQYDzH63OBl0E7NTeUmaBwKHKc3EjTIb" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.3/css/bootstrap-dialog.min.css" integrity="sha384-WRu9pYyIOqqid8N7J6OSeS1TzFeAvmPD2O7idI6zgta6+9cVPdjOzu/2GTEKmC7E" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" integrity="sha384-gOaRlqAhqPUMlR/5HfjaLm+COAJ+Ka0Am9GCueJAWwFluNWKDUZJ8GUGhBJ1r+J/" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css" integrity="sha384-rjXedYe/HKeDRcs0Euwr7zNsokaidJhDyzFkoUnsmTghcqseOcpdsbyqVScOXfk7" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" integrity="sha384-HIipfSYbpCkh5/1V87AWAeR5SUrNiewznrUrtNz1ux4uneLhsAKzv/0FnMbj3m6g" rel="stylesheet">
    <link href="{{ config('app.root') }}/files/css/AdminLTE.min.css" rel="stylesheet">
    <link href="{{ config('app.root') }}/files/css/skins/_all-skins.min.css" rel="stylesheet">
    @yield( 'additionalCss' )
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <a class="logo" href="{{ config('app.root') }}"><span class="logo-mini"><b>P</b>LM</span> <span class="logo-lg"><b>V</b>ERACiTY</span></a>
        <nav class="navbar navbar-static-top">
            <a class="sidebar-toggle" data-toggle="offcanvas" href="#" role="button"><span class="sr-only">Toggle navigation</span></a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><img alt="User Image" class="user-image" src="{{ Session::get( 'activeUserPhoto' ) }}"> <span class="hidden-xs">{{ Session::get( 'activeUserName' ) }}</span></a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img alt="User Image" class="img-circle" src="{{ Session::get( 'activeUserPhoto' ) }}">
                                <p>
                                    {{ Session::get( 'activeUserName' ) }} <small>{{ Session::get( 'activeUserEmail' ) }}</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a class="btn btn-default btn-flat" href="/settings">Settings</a>
                                </div>
                                <div class="pull-right">
                                    <a class="btn btn-default btn-flat" href="{{ route( 'getLogout' ) }}">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img alt="User Image" class="img-circle" src="{{ Session::get( 'activeUserPhoto' ) }}">>
                </div>
                <div class="pull-left info">
                    <p class="small">
                        Employee # {{ Session::get( 'activeUser' ) }}
                    </p><a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li class="header">NAVIGATION
                </li>
                <li class="@yield( 'dashboard-menu-active' ) treeview">
                    <a href="{{ config('app.root') }}/dashboard"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                </li>
                <li class="@yield( 'gp-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>General Payroll</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'gp-gen-active' )">
                            <a href="{{ config('app.root') }}/gp/report/generate"><i class="fa fa-circle-o"></i> <span>Generate Report</span></a>
                        </li>
                        <li class="@yield( 'gp-view-active' )">
                            <a href="{{ config('app.root') }}/gp/report/find"><i class="fa fa-circle-o"></i> <span>View Reports</span></a>
                        </li>
                    </ul>
                </li>
                <li class="@yield( 'pt-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Part-Time</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'pt-gen-active' )">
                            <a href="{{ config('app.root') }}/parttime/report/generate"><i class="fa fa-circle-o"></i> <span>Generate Report</span></a>
                        </li>
                        <li class="@yield( 'pt-view-active' )">
                            <a href="{{ config('app.root') }}/parttime/report/find"><i class="fa fa-circle-o"></i> <span>View Reports</span></a>
                        </li>
                        <li class="@yield( 'pt-summary-active' )">
                            <a href="{{ config('app.root') }}/parttime/report/summary"><i class="fa fa-circle-o"></i> <span>View Summary</span></a>
                        </li>
                        <li class="@yield( 'pt-signatories-active' )">
                            <a href="{{ config('app.root') }}/parttime/report/signatories"><i class="fa fa-circle-o"></i> <span>Signatories</span></a>
                        </li>
                    </ul>
                </li>
				  <li class="@yield( 'cos-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Contract Of Service</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'cos-gen-active' )">
                            <a href="{{ config('app.root') }}/cos/report/generate"><i class="fa fa-circle-o"></i> <span>Generate Report</span></a>
                        </li>
                        <li class="@yield( 'cos-view-active' )">
                            <a href="{{ config('app.root') }}/cos/report/find"><i class="fa fa-circle-o"></i> <span>View Reports</span></a>
                        </li>
                        <li class="@yield( 'cos-summary-active' )">
                            <a href="{{ config('app.root') }}/cos/report/summary"><i class="fa fa-circle-o"></i> <span>View Summary</span></a>
                        </li>
                        <li class="@yield( 'cos-signatories-active' )">
                            <a href="{{ config('app.root') }}/cos/report/signatories"><i class="fa fa-circle-o"></i> <span>Signatories</span></a>
                        </li>
                    </ul>
                </li>
                <li class="@yield( 'cp-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Casual</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'cp-gen-active' )">
                            <a href="{{ config('app.root') }}/casual/report/generate"><i class="fa fa-circle-o"></i> <span>Generate Report</span></a>
                        </li>
                        <li class="@yield( 'cp-view-active' )">
                            <a href="{{ config('app.root') }}/casual/report/find"><i class="fa fa-circle-o"></i> <span>View Reports</span></a>
                        </li>
                        <li class="@yield( 'cp-signatories-active' )">
                            <a href="{{ config('app.root') }}/casual/report/signatories"><i class="fa fa-circle-o"></i> <span>Signatories</span></a>
                        </li>
                    </ul>
                </li>
                <li class="@yield( 'ep-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Excluded</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'ep-gen-active' )">
                            <a href="{{ config('app.root') }}/excluded/report/generate"><i class="fa fa-circle-o"></i> <span>Generate Report</span></a>
                        </li>
                        <li class="@yield( 'ep-view-active' )">
                            <a href="{{ config('app.root') }}/excluded/report/find"><i class="fa fa-circle-o"></i> <span>View Reports</span></a>
                        </li>
                        <li class="@yield( 'ep-signatories-active' )">
                            <a href="{{ config('app.root') }}/excluded/report/signatories"><i class="fa fa-circle-o"></i> <span>Signatories</span></a>
                        </li>
                    </ul>
                </li>
                <!--<li class="@yield( 'extraLoadMenu' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Extra Load</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'elGenerateMenu' )">
                            <a href="/el/report/generate"><i class="fa fa-circle-o"></i> <span>Generate Report</span></a>
                        </li>
                        <li class="@yield( 'elViewMenu' )">
                            <a href="/el/report/find"><i class="fa fa-circle-o"></i> <span>View Reports</span></a>
                        </li>
                    </ul>
                </li>
                <li class="@yield( 'jo-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Job Order</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'jo-gen-active' )">
                            <a href="/jo/report/generate"><i class="fa fa-circle-o"></i> <span>Generate Report</span></a>
                        </li>
                        <li class="@yield( 'jo-view-active' )">
                            <a href="/jo/report/find"><i class="fa fa-circle-o"></i> <span>View Reports</span></a>
                        </li>
                        <li class="@yield( 'joParametersMenu' )">
                            <a href="/jo/params/list"><i class="fa fa-circle-o"></i> <span>Parameters</span></a>
                        </li>
                    </ul>
                </li>-->
                <li class="@yield( 'deductions-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Deductions</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'deductions-submenu1-active' )">
                            <a href="{{ config('app.root') }}/deductions/add"><i class="fa fa-circle-o"></i> Add Deduction</a>
                        </li>
                        <li class="@yield( 'deductions-submenu2-active' )">
                            <a href="{{ config('app.root') }}/deductions/view"><i class="fa fa-circle-o"></i> View Deductions List</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield( 'payslip-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Payslips</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'payslip-submenu1-active' )">
                            <a href="{{ config('app.root') }}/payslip/mail"><i class="fa fa-envelope-o"></i> Send Payslips</a>
                        </li>
                        <!-- <li class="@yield( 'payslip-submenu1-active' )">
                            <a href="/#"><i class="fa fa-print"></i> Print Payslips</a>
                        </li> -->
                    </ul>
                </li>
            </ul>
        </section>
    </aside>
    <div class="content-wrapper">
        <section class="content-header">
            @yield( 'pageSectionTitle' )
            @yield( 'breadCrumb' )
        </section>
        <section class="content">
            @yield( 'pageContent' )
        </section>
    </div>
    <footer class="main-footer">
        <strong>Copyright © <a href="http://www.plm.edu.ph/">Pamantasan ng Lungsod ng Maynila</a>.</strong>
        <div class="pull-right hidden-xs">
            AdminLTE v2.3.7 by <a href="http://almsaeedstudio.com">Almsaeed Studio</a>
        </div>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
<script crossorigin="anonymous" defer integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-KgEy7s3ThYKule8wWiu2WJkm0AmJeSLkXku5PY5X8MhVgdm8K1ebsVRKHfNfWPrR" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-BU6QNLIZsoGezWel6XIhlupzUX/fy/X5XrDyatPnvDMpnLzIKP3d2Iy9377QO7dj" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-S1tNzAhBOKZlAQtaGy+x7AUKzkUTF+T5yxdH3J7QIxPkRk9rtl5X6rdvUEXJ0qXz" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.3/js/bootstrap-dialog.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-BpOQ55RA3arDUEP5boNauNeeKbuCH9IAzxHZTwHM+BKw2N09+Wep2yWWyvnE5eZl" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-7PXRkl4YJnEpP8uU4ev9652TTZSxrqC8uOpcV1ftVEC7LVyLZqqDUAaq+Y+lGgr9" src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-222hzbb8Z8ZKe6pzP18nTSltQM3PdcAwxWKzGOKOIF+Y3bROr5n9zdQ8yTRHgQkQ" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-xVFFnJ+c6gNEcHVTSWdoMC2X9dzjxnMJ2fLcxlFHmNK5SuhHp4VndeSzG3k17Wl5" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/en.js"></script>
<script defer src="{{ config('app.root') }}/files/js/app_custom.js"></script>
<script>
    $( '#errorMessage' ).delay( 10000 ).fadeOut( 'slow' );
    $( '#successMessage' ).delay( 10000 ).fadeOut( 'slow' );
</script>
@yield( 'additionalJs' )
</body>
</html>
