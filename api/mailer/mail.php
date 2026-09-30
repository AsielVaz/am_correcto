<?php
include "../funciones.php";

        $mensaje='Se ha registrado un Movimiento de tu cuenta que se describe a continuación:
        <br /><br />
  

               <table border>
                  <thead>
                     <tr>
                        <th style="line-height: 1.42857;">ID INTERNO DE PAGO</th>
                        <th style="line-height: 1.42857;">EMPRESA</th>
                        <th style="line-height: 1.42857;">NOMBRE CORTO BANCO</th>
                        <th style="line-height: 1.42857;">FECHA DE ASIGNACIÓN</th>
                        <th style="line-height: 1.42857;">FECHA DE BANCO</th>
                        <th style="line-height: 1.42857;">TIPO DE MOVIMIENTO</th>
                        <th style="line-height: 1.42857;">MONTO ORIGINAL</th>
                        <th style="line-height: 1.42857;">DETALLE</th>
                        <th style="line-height: 1.42857;">DESCRIPCION</th>
                        <th style="line-height: 1.42857;">SALDO ACREDITADO</th>
                        <th style="line-height: 1.42857;">ORIGIEN COMISION</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td style="line-height: 1.42857;"><a>260</a></td>
                        <td style="line-height: 1.42857;"><a>LOGISTICA Y MANIOBRAS VEGAS S.A. DE C.V.</a></td>
                        <td style="line-height: 1.42857;"><a>LOGISTICA SANTANDER</a></td>
                        <td style="line-height: 1.42857;"><a>02/01/2019</a></td>
                        <td style="line-height: 1.42857;"><a>28/12/2018</a></td>
                        <td style="line-height: 1.42857;"><a>Retiro</a></td>
                        <td style="line-height: 1.42857;"><a>$9,278.00</a></td>
                        <td style="line-height: 1.42857;"><a></a></td>
                        <td style="line-height: 1.42857;"><a>PAGO TRAN SPEI</a></td>
                        <td style="line-height: 1.42857;"><a>$-9,278.00</a></td>
                        <td style="line-height: 1.42857;"><a></a></td>
                     </tr>
                     
                  </tbody>
               </table>
           

        ';
echo $mensaje;
//mailer("ingefcoalvarado@hotmail.com","Francisco Alvarado","Hola",utf8_decode($mensaje));
?>      