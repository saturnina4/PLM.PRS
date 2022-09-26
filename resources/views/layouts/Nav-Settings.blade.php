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
    <link href="/files/img/common/plm_seal_2014.png" rel="icon" type="image/x-icon">
    <link crossorigin="anonymous" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker.min.css" integrity="sha384-Ky52TgDe3RyvA0FQqi8LZdX4aK/heECxjmGgPu8GNO9eadWCglDYhP+KHSA//faj" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.3/css/bootstrap-dialog.min.css" integrity="sha384-WRu9pYyIOqqid8N7J6OSeS1TzFeAvmPD2O7idI6zgta6+9cVPdjOzu/2GTEKmC7E" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" integrity="sha384-gOaRlqAhqPUMlR/5HfjaLm+COAJ+Ka0Am9GCueJAWwFluNWKDUZJ8GUGhBJ1r+J/" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" integrity="sha384-p/NQoT0G1WaSOtpkNLDWe2nWstNl65yswLy5523OUNaS0Zg+2Pw+6P2OID9vA74O" rel="stylesheet">
    <link href="/files/css/AdminLTE.min.css" rel="stylesheet">
    <link href="/files/css/skins/_all-skins.min.css" rel="stylesheet">
    @yield( 'additionalCss' )
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <header class="main-header">
        <a class="logo" href="/"><span class="logo-mini"><b>P</b>LM</span> <span class="logo-lg"><b>V</b>ERACiTY</span></a>
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
                                    <a class="btn btn-default btn-flat" href="/logout">Sign out</a>
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
                <li class="@yield( 'settings-menu-active' ) treeview">
                    <a href="/settings"><i class="fa fa-gear"></i> <span>Settings</span></a>
                </li>
                <li class="@yield( 'tranche-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Salary Tranche</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'tranche-submenu1-active' )">
                            <a href="/settings/tranche/view"><i class="fa fa-eye"></i> View Tranche Table</a>
                        </li>
                        <li class="@yield( 'tranche-submenu2-active' )">
                            <a href="/settings/tranche/set"><i class="fa fa-check"></i> Set Active Tranche</a>
                        </li>
                        <li class="@yield( 'tranche-submenu3-active' )">
                            <a href="/settings/tranche/upload"><i class="fa fa-upload"></i> Upload Tranche Values</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield( 'payslip-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Signatories</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'payslip-submenu1-active' )">
                            <a href="/#"><i class="fa fa-envelope-o"></i> Send Payslips</a>
                        </li>
                        <li class="@yield( 'payslip-submenu1-active' )">
                            <a href="/#"><i class="fa fa-print"></i> Print Payslips</a>
                        </li>
                    </ul>
                </li>
                <li class="@yield( 'whtax-menu-active' ) treeview">
                    <a href="#"><i class="fa fa-money"></i> <span>Withholding Tax</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                    <ul class="treeview-menu">
                        <li class="@yield( 'gpayroll-regular-active' )">
                            <a href="#"><i class="fa fa-user"></i> <span>Regulars</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                            <ul class="treeview-menu">
                                <li class="@yield( 'gpayroll-regular-submenu1-active' )">
                                    <a href="/gp/regular/report/new"><i class="fa fa-plus"></i> New Report</a>
                                </li>
                                <li class="@yield( 'gpayroll-regular-submenu2-active' )">
                                    <a href="/gp/regular/report/view"><i class="fa fa-search"></i> View Reports</a>
                                </li>
                            </ul>
                        </li>
                        <li class="@yield( 'gpayroll-casual-active' )">
                            <a href="#"><i class="fa fa-user"></i> <span>Casuals</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                            <ul class="treeview-menu">
                                <li class="@yield( 'gpayroll-casual-submenu2-active' )">
                                    <a href="#"><i class="fa fa-plus"></i> New Report</a>
                                </li>
                                <li class="@yield( 'gpayroll-casual-submenu2-active' )">
                                    <a href="#"><i class="fa fa-search"></i> Find Reports</a>
                                </li>
                            </ul>
                        </li>
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

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha384-rY/jv8mMhqDabXSo+UCggqKtdmBfd3qC2/KvyTDNQ6PcUJXaxK1tMepoQda4g5vB" crossorigin="anonymous"></script>
<script>
    $.widget.bridge( 'uibutton', $.ui.button );
</script>
<script crossorigin="anonymous" defer integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-A8B4JWQBozE6apu98zlbPP2chvE7NP3zLqG/JyTpo0wdeozhXeXeuWkCy790GTty" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/moment.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-V936AaxnYTkq53cO2z8e42PFtCCdZhbRttfhgX8U1K9weNYVdCMbDetUKTtllFdc" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-S1tNzAhBOKZlAQtaGy+x7AUKzkUTF+T5yxdH3J7QIxPkRk9rtl5X6rdvUEXJ0qXz" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.3/js/bootstrap-dialog.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-89aj/hOsfOyfD0Ll+7f2dobA15hDyiNb8m1dJ+rJuqgrGR+PVqNU8pybx4pbF3Cc" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script crossorigin="anonymous" defer integrity="sha384-SR1gffNfWzqensZ3u8O8AkytPBwtg4pKQuOrHUvvCuAxqcoAE4LWPryg4o+1Y9uP" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script defer src="/files/js/app_custom.js"></script>
<script>
    $( '#errorMessage' ).delay( 10000 ).fadeOut( 'slow' );
    $( '#successMessage' ).delay( 10000 ).fadeOut( 'slow' );
</script>
@yield( 'additionalJs' )
</body>
</html>
