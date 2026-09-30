<?php
include "../funciones.php";

$mensaje = 'Se ha registrado un Movimiento de tu cuenta que se describe a continuación:
        <br /><br />


               
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>SISTEMA 14</title>
    <meta name="description" content="Sistema 14" />
    <meta name="keywords" content="" tent="Sistema 14" />
    <meta name="author" content="Francisco J. Alvarado"/>   

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/logo.ico">

  <!-- Sweet-Alert  -->
  <link href="vendors/bower_components/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
    
  <!-- Custom CSS -->
  <link href="dist/css/style.css" rel="stylesheet" type="text/css">    
    <script src="vendors/bower_components/sweetalert/dist/sweetalert.min.js"></script>
    <!--<link rel="stylesheet" href="styles/dashboard-style.css">-->

    <!-- jQuery -->
  <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>

  <!-- Bootstrap Core JavaScript -->
  <script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="dist/js/sweetalert-data.js"></script>
<link href="dist/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Chart.js -->
    <script src="dist-new/chart/Chart.bundle.js"></script>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader-it">
        <div class="la-anim-1"></div>
    </div>
    <!-- /Preloader -->
    <div class="wrapper">
            <!-- Top Menu Items -->
            <link rel="stylesheet" href="styles/dashboard-style.css">
<nav class="navbar navbar-inverse navbar-fixed-top">
    <a id="toggle_nav_btn" class="toggle-left-nav-btn inline-block mr-20 pull-left" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a class="navbar-brand" href="index.php?modo=Captura">
            <img class="logo" src="assets/logo.ico" width="42" height="42"/>
            </a>
        <ul class="nav navbar-right top-nav pull-right">

            <!-- <li>
                <a href="javascript:void(0);" data-toggle="collapse" data-target="#site_navbar_search">
                    <i class="fa fa-search top-nav-icon"></i>
                </a>
            </li> -->
            
            
            <!-- <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell top-nav-icon"></i><span class="top-nav-icon-badge">0</span></a>
                <ul  class="dropdown-menu alert-dropdown" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <li>
                        <div class="streamline message-box message-nicescroll-bar">
                            <div class="sl-item">
                                <div class="icon">
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <div class="sl-content">
                                    <a href="javascript:void(0)" class="inline-block capitalize-font  pull-left">Mensaje nuevo</a>
                                    <span class="inline-block font-12  pull-right">1pm</span>
                                    <div class="clearfix"></div>
                                        <p>celia@brandid.mx</p>
                                    </div>
                                </div>
                                <hr/>       
                            </div>
                        </li>
                    </ul>
            </li> -->

            <li class="dropdown">
                <a href="#" class="dropdown-toggle pr-0" data-toggle="dropdown">
                    <img src="dist/img/user1.png" alt="user_auth" class="user-auth-img img-circle"/>
                    <!-- <span class="user-online-status"></span> -->
                </a>

                <ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                    <li>
                        <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Salir</a>
                    </li>
                </ul>
                
            </li>
        </ul>



        <!-- <div class="collapse navbar-search-overlap" id="site_navbar_search">
            <form role="search">
                        <div class="form-group mb-0">
                            <div class="input-search">
                                <div class="input-group">   
                                    <input type="text" id="overlay_search" name="overlay-search" class="form-control pl-30" placeholder="Search">
                                    <span class="input-group-addon pr-30">
                                    <a  href="javascript:void(0)" class="close-input-overlay" data-target="#site_navbar_search" data-toggle="collapse" aria-label="Close" aria-expanded="true"><i class="fa fa-times"></i></a>
                                    </span> 
                                </div>
                            </div>
                        </div>
                    </form>
                </div> -->
</nav><div class="fixed-sidebar-left">
    <ul class="nav navbar-nav side-nav nicescroll-bar">
    
    
            
            <li>
                <a  class="active" href="javascript:void(0);" data-toggle="collapse" data-target="#dashboard_inv"><i class="icon-folder mr-10"></i>Reportes<span class="pull-right"><i class="fa fa-fw fa-angle-down"></i></span></a>
                <ul id="dashboard_inv" class="collapse collapse-level-1">
                    <li>
                        <a class="active" href="accesos.php">Accesos al Sistema</a>
                        
                    </li>
                    <li>
                        <a class="active" href="bitacora.php">Bitacora General</a>
                        
                    </li>
                </ul>
            </li>

            <li>
                <a  class="active" href="javascript:void(0);" data-toggle="collapse" data-target="#dashboard_gen_pdf"><i class="icon-folder mr-10"></i>Catalogos<span class="pull-right"><i class="fa fa-fw fa-angle-down"></i></span></a>
                <ul id="dashboard_gen_pdf" class="collapse collapse-level-1">
                    <li>
                        <a class="active" href="empresas.php">Empresas</a>
                        <a class="active" href="conceptos.php">Conceptos facturables por empresa</a>
                        <a class="active" href="alta_documentos.php">Alta de documentos</a>
                        <a class="active" href="tipos_clientes.php">Tipos de clientes</a>
                        <a class="active" href="bancos.php">Bancos</a>
                        <a class="active" href="clientes.php">Clientes</a>
                        <a class="active" href="claves_cortas.php">Claves Cortas de Clientes</a>
                        <a class="active" href="formas_pago.php">Formas de Pago</a>
                        <a class="active" href="usuarios.php">Usuarios del sistema</a>
                    </li>
                </ul>
            </li>

            <li>
                <a  class="active" href="javascript:void(0);" data-toggle="collapse" data-target="#dashboard_facturacion"><i class="icon-folder mr-10"></i>Facturacion<span class="pull-right"><i class="fa fa-fw fa-angle-down"></i></span></a>
                <ul id="dashboard_facturacion" class="collapse collapse-level-1">
                    <li>
                        <a class="active" href="genera_factura.php">Ejemplo de facturacion</a>
                    </li>
                    <li>
                        <a class="active" href="gen_factura_man.php">Facturas Manuales</a>
                    </li>
                    <li>
                        <a class="active" href="gen_factura_aut.php">Facturas Automaticas</a>
                    </li>
                </ul>
            </li>
            <li>
                <a  class="active" href="javascript:void(0);" data-toggle="collapse" data-target="#dashboard_reportes"><i class="icon-folder mr-10"></i>Movimientos<span class="pull-right"><i class="fa fa-fw fa-angle-down"></i></span></a>
                <ul id="dashboard_reportes" class="collapse collapse-level-1">
                    <li>
                        <a class="active" href="pagos_proc.php">Movimientos Asignados</a>
                        
                    </li>
                    <li>
                        <a class="active" href="pagos_sin_proc.php">Movimientos Sin Asignar</a>
                        
                    </li>
                    <li>
                        <a class="active" href="desasignacion.php">Des-asignar Movimientos</a>
                        
                    </li>
                    <li>
                        <a class="active" href="traspasos.php">Traspaso entre Clientes</a>
                        
                    </li>
                </ul>
            </li>
            <li>
                <a  class="active" href="javascript:void(0);" data-toggle="collapse" data-target="#dashboard_captura"><i class="icon-folder mr-10"></i>Captura<span class="pull-right"><i class="fa fa-fw fa-angle-down"></i></span></a>
                <ul id="dashboard_captura" class="collapse collapse-level-1">
                    <li>
                        <a class="active" href="captura.php">Captura</a>
                    </li>
                    <li>
                        <a class="active" href="captura_man.php">Captura Manual</a>
                    </li>
                </ul>
            </li>
            <li>
                <a  class="active" href="javascript:void(0);" data-toggle="collapse" data-target="#configuracion"><i class="icon-folder mr-10"></i>Configuracion<span class="pull-right"><i class="fa fa-fw fa-angle-down"></i></span></a>
                <ul id="configuracion" class="collapse collapse-level-1">
                    <li>
                        <a class="active" href="configuracion.php">Configuracion General</a>
                    </li>
                    
                </ul>
            </li>
                    

    </ul>
</div>          <div class="page-wrapper">
<div class="container-fluid">
    <!-- Title -->
    <div class="row heading-bg bg-blue">
      <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <a href="index.php"><h5 class="txt-light">Sistema 14</h5></a>
        </div>
        <div  class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                        <p align="right" style="color:#fff; font-size:16px;">Tipo de Cambio: $19.14</p>
            <p align="right">
                <form action="index.php" method="POST" enctype="multipart/form-data">
                    

                    <input type="hidden" name="modo" value="">

                                        <p align="right"><input type="submit" class="btn btn-default"  style="display:scroll; z-index: 3; 
        position:fixed;
        top:100px;
        right:5px;" value="Cambiar a Modo Administrador"></p>

                    <p align="right"><a href="#clientes" class="btn btn-default" style="display:scroll; z-index: 3; 
        position:fixed;
        top:160px;
        right:5px;">Ir a Clientes</a></p>

                    
                </form>
            </p>

        </div>

            <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <!-- <li><a href=#>Panel de Control</a></li> -->
                <li class="active"><span></span></li>
            </ol>
        </div>
    </div>

        <!-- <p align="center" style="font-size: 28px;">Noticias</p><br />
        <hr style="height: 3px; background-color: #6B6B6B; color: #6B6B6B; border-top: #6B6B6B !important ;" /> -->

    <div class="row">
        <div class="col-sm-12">
            <form action="busqueda_general.php" method="POST" enctype="multipart/form-data" class="form-horizontal">
                <div class="col-md-8">
                        <input type="text" name="busqueda_general" autofocus placeholder="Busqueda General" maxlength="100" class="form-control ">
                </div>
                <div class="col-md-4">
                        <input type="submit" class="btn btn-primary">
                </div>
            </form>

        </div>
    </div>
    <br />
    <div class="row">
                <div class="col-md-2">  

            <div class="main container-fluid" style="background-color:#A53636; color:#fff">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;">
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>CARENC</li></h6></strong><br>
                        </div>
                    </div>

                    <a href="reporte_clientes.php?token=f3c74c5305429315747aa49e2208b0ae513c9de0&amp;c=1" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #fff; margin-top:10px;">$277,037.53</p></strong>
                    
            </div>      
        </div>
                <div class="col-md-2">  
            <div class="main container-fluid" style="background-color: #D7D7D7; color: #fff;">
                <h5 style="color: #000; margin-top:-40px;"><strong>Movimientos Pendientes</strong></h5><br />

                                <strong><p align="right" style="color: #000; font-size:20px;">2</p></strong>
                <strong><p align="right" style="color: #000; font-size:20px;">$ 947.14</p></strong>
            
            </div>
        </div>

        <div class="col-md-2">  
            <div class="main container-fluid">
                <h5>Diferencia</h5>

                                <strong><p align="right" style="font-size:20px;">$0.00</p></strong>
            
            </div>
        </div>
        <div class="col-md-2">  
            <div class="main container-fluid" style="background-color: #78A5FF; color: #fff;">
                <h5 style="color: #fff;">Dinero</h5><br />
                <strong><p align="right" style="font-size:20px;" id="saldo">$0.00</p></strong>
                
            </div>
        </div>


        
        <div class="col-md-4">  
            <div class="main container-fluid" style="background-color: #fff; color: #000;">
                <h5 style="color: #000;">Calculadora</h5><br />
                <form action="index.php" id="validation-form" class="form-horizontal" method="post" enctype="multipart/form-data">
                    

                        <select name="calculadora_modo" id="regimen" class="form-control rounded-input">
                        <option selected value="necesito">Dinero que necesito</option>
                        <option value="queda">Dinero que me queda</option>
                        </select>

                        
  <div class="col-md-12">
    <label for="id_cliente" class="control-label mb-10 text-left">Cliente</label>
         <select name="id_cliente" id="id_cliente"  class="form-control rounded-input">
          <option value=""></option>
<option  value="16">AARON</option>
<option  value="19">ADRIAN</option>
<option  value="5">BRACHO</option>
<option  value="1">CARENC</option>
<option  value="11">CARPIO</option>
<option  value="18">COLAB</option>
<option  value="12">COMPADRE</option>
<option  value="17">DENISSE</option>
<option  value="14">ECO</option>
<option  value="9">INSTANT LUNCH</option>
<option  value="13">interno</option>
<option  value="8">MAMI BRACHO</option>
<option  value="7">PAQUITO</option>
<option  value="10">SALDE</option>
<option  value="4">SANDRA MARTINEZ NAVARRO</option>
<option  value="6">TOÑO</option>
</select>
  </div>

                        <input type="number" name="dinero" size="18" maxlength="100" step="0.01" class="form-control rounded-input  ">
                        <input type="hidden" value="si" name="calculo" size="18" maxlength="100" class="form-control rounded-input">
                        <input type="submit" class="btn btn-primary">


                                            
                </form>
            
            </div>
        </div>
        

    </div>  

        <br />
<hr style="border-top: 1px solid #cacaca;" />
    <p align="center" style="font-size: 22px;">Empresas</p>
    <br />


        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="10">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $16,818,415.32</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 65</p>
            <h5>ZAC BANQUETES Y ALIMENTOS S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=14&razon=10">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">ZAC SANTANDER 
                
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$799.67</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("10").innerHTML = "MXN $799.67";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$799.67";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="14">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $0.00</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 0</p>
            <h5>YODAZAC MATERIAS PRIMAS DE OCCIDENTE S.A DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
<script>
document.getElementById("14").innerHTML = "MXN $0.00";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$799.67";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="7">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $4,775,259.34</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 17</p>
            <h5>PROMOTORA COMERCIAL PUBLICITARIA DE EVENTOS, S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=7&razon=7">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">PROMOTORA SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$2,103.00</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=19&razon=7">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Banorte</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF9D35" style="color:#fff !important;">PROMOTORA BANORTE 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$38,590.03</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("7").innerHTML = "MXN $40,693.03";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$41,492.70";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="9">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $2,253,804.96</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 13</p>
            <h5>PARIS PUBLICIDAD APLICADA S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=15&razon=9">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">PARIS SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$23,972.04</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("9").innerHTML = "MXN $23,972.04";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$65,464.74";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="6">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $19,504,291.34</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 72</p>
            <h5>OUTSOURCING COMPLEMENTO EMPRESARIAL S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=17&razon=6">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">OUTSOURCING SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$108,600.36</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=18&razon=6">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Banorte</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF9D35" style="color:#fff !important;">OUTSOURCING BANORTE 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$9,399.31</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("6").innerHTML = "MXN $117,999.67";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$183,464.41";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="5">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $8,383,825.68</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 45</p>
            <h5>NALDUR COMMERCE MEXICO, S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura_man.php?accion=captura&nombre_corto=1&razon=5">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Manual</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FFEB84" style="color:#000 !important;">BASE pesos 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$10,747.56</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=4&razon=5">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Bancomer</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="747FDA" style="color:#fff !important;">NALDUR BANCOMER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$95,929.14</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=5&razon=5">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">NALDUR SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$593.73</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=6&razon=5">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Banorte</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF9D35" style="color:#fff !important;">NALDUR BANORTE 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$390.00</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura_man.php?accion=captura&nombre_corto=20&razon=5">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Manual</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="74C7DA" style="color:#000 !important;">NALDUR MONEX 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$0.00</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura_man.php?accion=captura&nombre_corto=23&razon=5">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Manual</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FFEB84" style="color:#000 !important;">BASE dolares 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                <th bgcolor="FFEB84" style="color:#000 !important;"></th>
                    <tr>
                        <td style="color: #000 !important;;">Saldo en Dolares: $1.00</td>
                        <td style="color: #000 !important;;">Saldo en Pesos: $19.14</td>
                    </tr>

                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("5").innerHTML = "MXN $107,679.57";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$291,143.98";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="11">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $69,600.00</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 1</p>
            <h5>MILAN RENTA DE MAQUINARIA S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=13&razon=11">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">MILAN SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$71,871.16</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("11").innerHTML = "MXN $71,871.16";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$363,015.14";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="4">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $7,486,224.19</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 32</p>
            <h5>MANIOBRAS DEL PACIFICO NALDUR MANZANILLO, S. A. DE C. V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=8&razon=4">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Banorte</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF9D35" style="color:#fff !important;">MANIOBRAS BANORTE PESOS 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$716,164.53</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=11&razon=4">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">MANIOBRAS SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$9,520.49</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("4").innerHTML = "MXN $725,685.02";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$1,088,700.16";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="12">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $6,781,974.43</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 31</p>
            <h5>LOGISTICA Y MANIOBRAS VEGAS S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=9&razon=12">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Banorte</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF9D35" style="color:#fff !important;">LOGISTICA BANORTE PESOS 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$311,340.95</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=10&razon=12">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">LOGISTICA SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$417,855.73</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("12").innerHTML = "MXN $729,196.68";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$1,817,896.84";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="13">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $15,917,850.07</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 64</p>
            <h5>EDWORLD SOLUCIONES ADMINISTRATIVAS INTEGRALES S.C.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=16&razon=13">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">EDWORLD SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$26,866.15</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=21&razon=13">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Banorte</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF9D35" style="color:#fff !important;">EDWORLD BANORTE 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$57,343.34</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("13").innerHTML = "MXN $84,209.49";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$1,902,106.33";
</script>




            
            </div>
        </div>

        <br />
        <div class="main container-fluid">
            <strong><p align="right" style="font-size:18px; color: black;" id="8">$0.00</p></strong>
            
            <p align="right" style="font-size:18px;color: black;">Dinero sin factura $609,633.00</p>
            <p align="right" style="font-size:18px; color: black;">Movimientos sin Facturar: 4</p>
            <h5>CONSTRUCCIONES Y DESARROLLOS SUSTENTABLES CABO S.A. DE C.V.</h5><br />
            <!--  Row de contenido principal  -->
            <div class="row">
            
            <a href="captura.php?accion=captura&nombre_corto=12&razon=8">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Santander</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF5C5C" style="color:#fff !important;">CONSTRUCCIONES SANTANDER 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$5,573.60</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
            <a href="captura.php?accion=captura&nombre_corto=22&razon=8">
              <div class="col-sm-4 col-md-4 col-lg-4">
                <h6>Banorte</h6>
                
                <table class="tablesaw table-striped table-hover table-bordered table" data-tablesaw-mode="columntoggle">   
                <th bgcolor="FF9D35" style="color:#fff !important;">CONSTRUCCIONES BANORTE 
                <span class="badge" style="background-color:red !important; color: #EFEFEF; margin-top: 0px; 
    margin-right: 3px; 
    position:absolute; 
    top:0; 
    font-weight: bold;
    right:0;  font-size: 18px;"> ! </span>
                </th>
                
                    <tr>
                        <td style="color: #000 !important;;">$39,422.00</td>
                    </tr>


                    
                </table>            
              </div>
             </a>
<script>
document.getElementById("8").innerHTML = "MXN $44,995.60";
</script>
  

<script>
document.getElementById("saldo").innerHTML = "$1,947,101.93";
</script>




            
            </div>
        </div>

        <br />
<div id="clientes">     
<hr style="border-top: 1px solid #cacaca;" />
    <p align="center" style="font-size: 22px;">Clientes</p>

        
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#74C7DA; color:#fff">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>TOÑO</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #fff; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$0.00</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #fff; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">0</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=330808bce2622a604bfc7d5ec027babe111c5752&cliente=6" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #fff; margin-top:10px;">$52.67</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#A53636; color:#fff">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>SANDRA MARTINEZ NAVARRO</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #fff; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$1,646,952.43</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #fff; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">6</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=330808bce2622a604bfc7d5ec027babe111c5752&cliente=4" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #fff; margin-top:10px;">$0.00</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#FFEB84; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>SALDE</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$5,604,028.14</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">28</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=330808bce2622a604bfc7d5ec027babe111c5752&cliente=10" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$5,308.11</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#7ADA74; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>PAQUITO</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$2,135,940.08</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">23</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=330808bce2622a604bfc7d5ec027babe111c5752&cliente=7" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$55,017.46</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#FFEB84; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>MAMI BRACHO</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$193,317.92</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">2</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=330808bce2622a604bfc7d5ec027babe111c5752&cliente=8" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$189,217.64</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#FF5C5C; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>interno</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$71,220,712.85</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">259</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=13" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$0.00</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#FFEB84; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>INSTANT LUNCH</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$0.00</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">0</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=9" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$12,676.76</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#7ADA74; color:#fff">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>ECO</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #fff; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$196,843.29</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #fff; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">4</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=14" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #fff; margin-top:10px;">$-0.01</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#FFEB84; color:#">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>DENISSE</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$9,026.49</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">3</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=17" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #; margin-top:10px;">$-0.00</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#000080; color:#fff">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>COMPADRE</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #fff; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$7,300.00</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #fff; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">2</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=12" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #fff; margin-top:10px;">$7,300.00</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#FFEB84; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>COLAB</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$21,452.05</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">1</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=18" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$-0.00</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#74C7DA; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>CARPIO</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$165,000.00</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">1</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=11" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$0.00</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#A53636; color:#fff">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>CARENC</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #fff; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$40,754.00</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #fff; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">4</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=1" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #fff; margin-top:10px;">$277,037.53</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#FFEB84; color:#000">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>BRACHO</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #000; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$723,909.40</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #000; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">6</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=5" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #000; margin-top:10px;">$1,399,080.73</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#74C7DA; color:#fff">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>ADRIAN</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #fff; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$624,970.78</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #fff; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">2</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=19" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #fff; margin-top:10px;">$-0.00</p></strong>
                    
                    </div>
                </div>

            
                <div class="col-lg-2 col-md-3 col-sm-6" style="margin-top:10px;">
                    <div class="main container-fluid" style="background-color:#C683EA; color:#adadad">
                    <div class="row">
                        <div class="col-sm-12">
                            <hr style=" border-top-color: #fff; color: #fff;background-color: #fff; height: 50px; margin-top: -35px; margin-left:-50px; margin-right:-50px;" />
                            <strong><h6 style="color:#000; margin-left: -40px; margin-right: -40px; margin-top: -75px;"><li>AARON</li></h6></strong><br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px;color:  #adadad; margin-left:-30px; margin-right: -30px; margin-top: 5px;">$ Sin Factura <span class="badge badge-light">$9,741.90</span></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <p align="left" style="font-size:14px; color:  #adadad; margin-left:-30px; margin-right: -30px; ">Mov Pendientes: <span class="badge badge-light">1</span></p>
                        </div>
                    </div>


                    <a href="ejecuta_reporte_c.php?token=6f98a5d9460c7c88c4c1e3ab671c1797872e6316&so=0e6ac50f0225e4c8e9babffe16f104c089df2979&cliente=16" class="btn btn-primary" style="color:#fff; margin-top: 4px; margin-left: 55%;font-size:16px;padding: 1px 10px;">+ Detalles</a>

                        <strong><p align="left" style="font-size:17px; color: #adadad; margin-top:10px;">$463.90</p></strong>
                    
                    </div>
                </div>

            
</div>
    </div> <!-- container  -->
    <br>

        <!-- Modals Traspasos pendientes  -->
        <div class="modal fade" id="traspasos1" tabindex="-1" role="dialog" aria-labelledby="traspasos1Label" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="traspasos1Label">Modal title 1</h5>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-pink" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="traspasos2" tabindex="-1" role="dialog" aria-labelledby="traspasos2Label" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="traspasos2Label">Modal title 2</h5>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-pink" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
<footer class="footer container-fluid pl-30 pr-30">
    <div class="row">
        <div class="col-sm-5">
            <a href="index.php" class="brand mr-30">
                <img src="assets/logo.ico" width="42" height="42"/>
            </a>
            <ul class="footer-link nav navbar-nav">
                <!-- <li class="logo-footer"><a href="#">Ayuda</a></li>
                <li class="logo-footer"><a href="#">Aviso de Privacidad</a></li> -->
            </ul>
        </div>
        <div class="col-sm-7 text-right">
            <p>2018 &copy; Sistema 14</p>
        </div>
    </div>
</footer>           <!-- /Main Content -->

    </div>
    <!-- /#wrapper -->
                <!-- JavaScript -->
    
    <!-- jQuery -->
   
    
    <!-- Data table JavaScript -->
    
    
    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>
    <!-- simpleWeather JavaScript -->
    <script src="vendors/bower_components/moment/min/moment.min.js"></script>
    <script src="vendors/bower_components/simpleWeather/jquery.simpleWeather.min.js"></script>
    <script src="dist/js/simpleweather-data.js"></script>
    
    <!-- Progressbar Animation JavaScript -->
    <script src="vendors/bower_components/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="vendors/bower_components/Counter-Up/jquery.counterup.min.js"></script>
    
    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>
    
    <!-- Sparkline JavaScript -->
    <script src="vendors/jquery.sparkline/dist/jquery.sparkline.min.js"></script>
    
    <!-- ChartJS JavaScript -->
    <script src="vendors/chart.js/Chart.min.js"></script>
    
    <!-- Morris Charts JavaScript 
    <script src="vendors/bower_components/raphael/raphael.min.js"></script>
    <script src="vendors/bower_components/morris.js/morris.min.js"></script>
    <script src="dist/js/morris-data.js"></script>
    
    <script src="vendors/bower_components/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
    -->
    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <script src="dist/js/dashboard-data.js"></script>


                
        <!-- wysuhtml5 Plugin JavaScript -->
        <!-- jQuery -->
        
        <!-- Tinymce JavaScript -->
        <script src="vendors/bower_components/tinymce/tinymce.min.js"></script>
            
        <!-- Tinymce Wysuhtml5 Init JavaScript -->
        <script src="dist/js/tinymce-data.js"></script>         
        <!-- Tinymce Wysuhtml5 Init JavaScript -->
                <!--
  <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">-->
  <!-- <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>  -->
 <!-- <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> -->
 
  <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css" rel="stylesheet">
  <script src="summernote.js"></script> <!-- Fancy Dropdown JS -->
    
        
        <!-- Init JavaScript -->
        <script src="dist/js/init.js"></script>
    <!-- Fancy Dropdown JS -->
    


    <!-- Alertas -->
    



    
</body>

</html>

           

        ';
echo $mensaje;
//mailer("ingfcoalv@gmail.com","Francisco Alvarado","Hola",$mensaje);
