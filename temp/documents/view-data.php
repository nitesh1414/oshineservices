<?php

error_reporting(0);

$invno = $_POST['invno'];
$invdt = $_POST['invdt'];
$pono = $_POST['pono'];
$bpclcode = $_POST['bpclcode'];
$comp = $_POST['comp'];
$srno = $_POST['srno'];
$itemdesc = $_POST['itemdesc'];
$hsnsac = $_POST['hsnsac'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];

//$number = 12345.5667;
//$price = number_format((float)$price, 2);

$unit = $_POST['unit'];
$total = $_POST['total'];
$taxRate = $_POST['taxRate'];
$sor = $_POST['sor'];
$gst_amt = $_POST['gst_amt'];
$ftotal = $_POST['ftotal'];
$subTotal = $_POST['subTotal'];
$taxAmount = $_POST['taxAmount'];
$totalAftertax = $_POST['totalAftertax'];
$roundoff = $_POST['roundoff'];
$finalamount = $_POST['finalamount'];


$hsn = array_unique($hsnsac);

$tv = 0;
$rcgst = 0;
$acgst = 0;
$rsgst = 0;
$asgst = 0;

$finalhsn = array();


for ($j = 0; $j < count($hsn); $j++) {
	for ($i = 0; $i < count($hsnsac); $i++) {
		if($hsn[$j] == $hsnsac[$i])
		{
			$tv = $tv + $total[$i];
			$rcgst = $taxRate[$i] / 2;
			$acgst = $gst_amt[$i] / 2;
			$rsgst = $taxRate[$i] / 2;
			$asgst = $gst_amt[$i] / 2;
		}

		$finalhsn[$j] = [$hsn[$i], $tv, $rcgst, $acgst, $rsgst, $asgst];

	}
}




$cid = $_POST['comp'];

if ($cid == 2) {
	$invname = "Oshin-Services.pdf";
	$logo = "https://oshinservices.in/temp/documents/images/os-logo.png";
	$name = "Oshin Services";
	$address = "Plot no. 97, Behind Buddha Vihar, Nav Ekar, Chandramani Nagar, Nagpur - 440027";
	$email = "admin@oshinservices.in";
	$contact = "+91-7385283073";
	$gstin = "27EHXPS8451K1Z4";
	$state = "Maharashtra";
	$statecode = "27";
	$bpclcode = "";
	$billedto = "The Territory Manager (RS), Bharat Petroleum Corporation Limited";
	$placeofsuplly = "Nagpur, Maharashtra";
	$vgstin = "27AAACB2902M1ZT";
	$vaddress = "Business Process Excellence Centre (BPEC) 8th Floor, BPCL Office Complex, Plot No.6, Sector-2, Behind Cidco Garden, Kharghar, New Mumbai - 410210";
	$bankname = "Canara Bank";
	$branch = "Khamla, Nagpur";
	$accno = "120031969315";
	$ifsc = "CNRB0002810";
	$terms = '<ul><li>Make all checks payable to "Oshin Services".</li> <li> No responsibility after delivery.</li> <li>Payment must be made within 15 Days.</li> <li>"We declare that this invoice shows the actual price of the goods described & that all particulars are true and correct."</li></ul>';
	$color = "#ff9a00";
	$bgcolor = "#fce6c4";
	$bgcolor1 = "#fbd190";
} else if ($cid == 1) {
	$invname = "Friends-Enterprises.pdf";
	$logo = "https://oshinservices.in/temp/documents/images/fe-logo-small.png";
	$name = "Friends Enterprises";
	$address = "Plot no. 97, Behind Buddha Vihar, Nav Ekar, Chandramani Nagar, Nagpur - 440027";
	$email = "friendsent4821@gmail.com";
	$contact = "+91-9665861776";
	$gstin = "27EHXPS8451K1Z4";
	$state = "Maharashtra";
	$statecode = "27";
	$bpclcode = "381776";
	$billedto = "The Territory Manager (RS), Bharat Petroleum Corporation Limited";
	$placeofsuplly = "Nagpur, Maharashtra";
	$vgstin = "27AAACB2902M1ZT";
	$vaddress = "Business Process Excellence Centre (BPEC) 8th Floor, BPCL Office Complex, Plot No.6, Sector-2, Behind Cidco Garden, Kharghar, New Mumbai - 410210";
	$bankname = "Canara Bank";
	$branch = "Khamla, Nagpur";
	$accno = "120031969315";
	$ifsc = "CNRB0002810";
	$terms = '<ul><li>Make all checks payable to "Friends Enterprises".</li> <li> No responsibility after delivery.</li> <li>Payment must be made within 15 Days.</li> <li>"We declare that this invoice shows the actual price of the goods described & that all particulars are true and correct."</li></ul>';
	$color = "#0f4e84";
	$bgcolor = "#f6fafd";
	$bgcolor1 = "#b3d6ec";
}

//$output = '<style>table { color: ". $color ." }</style>';
//$output .= '<table><tr><td align="center" style = "text-align: center ;color = " . $color ." !important><h1>INVOICE</h1></td></tr></table>';

$output = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
$output .= '<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
	<td align="center" style="font-size:28px; color: ' . $color . '; font-family: Arial, Helvetica, sans-serif !important"><b>INVOICE</b></td>
	</tr>
	<tr>
	<td>
	<table width="100%" cellpadding="0" cellspacing="0" style="color: ' . $color . ' !important; font-family: Arial, Helvetica, sans-serif !important">
    <tr>
    <td colspan="6" style="background-color: ' . $bgcolor . '; color: ' . $color . ' !important;  border-bottom: 1px solid #000;">
    <img src="'. $logo .'" style="float:left; margin-top:20px">
    
    <p style="padding-left: 120px; font-weight:700; width: 300px; font-size:12px">
    <b style="font-size:16px">' . $name . '</b><br>' . $address . '<br>' . $contact . '<br> ' .  $email . '<br>' . $gstin  . '<br>' . $state . 'Code : ' . $statecode . '
	</p>
    </td>
	<td colspan="5" style="text-align:right; background-color:' . $bgcolor1 . '; border-bottom: 1px solid #000; font-size:12px;">
	<p style="border-bottom: 1px solid #000; padding:0 10 0 0; margin:0; font-family: Arial, Helvetica, sans-serif; font-weight:700">Invoice No.<br><b>' . $invno . '</b></p>
	<p style="border-bottom: 1px solid #000; padding:0 10 0 0; margin:0; font-family: Arial, Helvetica, sans-serif; font-weight:700">Invoice Date<br><b>' . $invdt . '</b></p>
	<p style="border-bottom: 1px solid #000; padding:0 10 0 0; margin:0; font-family: Arial, Helvetica, sans-serif; font-weight:700">PO No.<br><b>' . $pono . '</b></p>
	<p style="padding:0 10 0 0; margin:0; font-family: Arial, Helvetica, sans-serif; font-weight:700">BPCL Vendor Code<br><b>' . $bpclcode . '</b></p>

	</td>

    </tr>
	<tr>
	<td colspan="11" style="text-align:right; background-color:' . $bgcolor1 . '; border-bottom: 1px solid #000; padding:0px; margin:0px">
	<table width="100%"  cellpadding="5" cellspacing="0" style="font-size: 12px">
	<tr>
	<td width="50%" style="border-bottom: 1px solid #000;">
	<b>Bill To</b>
	</td>
	
	<td width="50%" height="20px" style="border-bottom: 1px solid #000;">
	<b>Address</b>
	</td>
	</tr>
	<tr>
	<td><b>
	' . $billedto . '<br>' . $placeofsuplly . ' GSTIN: ' . $vgstin . '</b>
	</td>
	<td><b>
		' . $vaddress . '</b>
	</td>
	</tr>

	</table>
	</td>
	
	</tr>
	</table>
	
	<table width="100%" border="1" cellpadding="2" cellspacing="0"  style="margin-top:5px; font-family:Arial, Helvetica, sans-serif !important; font-size:12px">
	<tr style="background-color:' . $bgcolor1 . '">
	<th>Sr No.</th>
	<th width="20%">Item Description</th>
	<th>HSN/SAC</th>
	<th>SOR</th>
	<th>Qty</th>
	<th>Rate</th>
	<th>Unit</th> 
	<th>Taxable Value</th>
	<th>GST %</th>
	<th>GST Amount</th>
	<th>Line Amount</th>
	</tr>';
$count = 0;
for ($i = 0; $i < count($srno); $i++) {
	$count++;
	$output .= '
	<tr>
	<td align="center">' . $count . '</td>
	<td align="left">' . $itemdesc[$i] . '</td>
	<td align="center">' . $hsnsac[$i] . '</td>
	<td align="center">' . $sor[$i] . '</td>
	<td align="center">' . $quantity[$i] . '</td>
	<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $price[$i] = number_format($price[$i], 2) . '</td>
	<td align="center">' . $unit[$i] . '</td>
	<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $total[$i] = number_format($total[$i], 2) . '</td>
	<td align="center">' . $taxRate[$i] . '%</td>
	<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $gst_amt[$i] = number_format($gst_amt[$i], 2) . '</td>
	<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $ftotal[$i] =  number_format($ftotal[$i], 2) . '</td>   
	</tr>';
}

$remain = 15 - count($srno) - count($hsn);

for ($i = 0; $i < $remain; $i++) {
$output .= '
	<tr>
	<td align="center">  </td>
	<td align="left"></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="center"></td>
	<td align="right"> &nbsp; </td>
	<td align="center"></td>
	<td align="right">  </td>
	<td align="center"></td>
	<td align="right"> </td>
	<td align="right">  </td>   
	</tr>';

}

$output .= '
	<tr style="background-color:' . $bgcolor1 . '">
	<td colspan="5" rowspan="3">
	<h4>Bank Details</h4>
        Bank Name: ' . $bankname .   '<br>
        Branch Name: ' . $branch . ' <br>
        Account No: ' . $accno . ' <br>
        IFSC Code: ' . $ifsc . ' 


	</td>
	<td align="right" colspan="2"><b>Total Amount</b></td>
	
	<td align="right">
	<b> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $subTotal =  number_format($subTotal, 2) . '</b>
	</td>
	<td></td>
	<td align="right">
	<b> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $taxAmount =  number_format($taxAmount, 2) . '</b>
	</td>
	<td align="right"> <b> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $totalAftertax =  number_format($totalAftertax, 2) . ' </b></td>
	
	</tr>
	
	<tr style="background-color:' . $bgcolor1 . '">
	<td align="right" colspan="2"><b>Round Off</b></td>
	<td colspan="4" align="right"> <b> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $roundoff =  number_format($roundoff, 2) . '</b></td>
	</tr>

	<tr style="background-color:' . $bgcolor1 . '">
	<td align="right" colspan="2"><b>Final Total</b></td>
	<td colspan="4" align="right"> <b> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $finalamount =  number_format($finalamount, 2)  . '</b></td>
	</tr>';
$output .= '
	</table>
	</td>
	</tr>
	</table>';

$output .= '<table width="100%" border="1" cellpadding="2" cellspacing="0" style="margin-top:5px; font-family:Arial, Helvetica, sans-serif !important; font-size:12px">
                        <tr>
                           <th>
                              Sr. No.
                           </th>
                           <th>
                              HSN/SAC NO
                           </th>
                           <th>
                              Taxable Value
                           </th>
                           <th colspan="2">
                              Central Tax (CGST)
                           </th>
                           <th colspan="2">
                              State Tax (SGST)
                           </th>
                        </tr>
						<tr>
                           <th>

                           </th>
                           <th>

                           </th>
                           <th>

                           </th>
                           <th>
                              Rate
                           </th>
                           <th>
                              Amount
                           </th>
                           <th>
                              Rate
                           </th>
                           <th>
                              Amount
                           </th>
                        </tr>';
$count = 0;
for ($i = 0; $i < count($finalhsn); $i++) {

	$count++;
	$output .= '
	<tr>
	<td align="center">' . $count . '</td>
	<td align="center">' . $finalhsn[$i][0] . '</td>

	<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $finalhsn[$i][1] . ' </td>
	
	<td align="right"> ' . $finalhsn[$i][2] . '% </td>
	
	<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span> ' . $finalhsn[$i][3] . ' </td>
	<td align="right"> ' . $finalhsn[$i][4] . '% </td>  
	<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>' . $finalhsn[$i][5] . ' </td> 
	</tr>';
}
$tatotal = 0;
$rctotal = 0;
$rstotal = 0;
for ($i = 0; $i < count($finalhsn); $i++) {

$tatotal = $tatotal + $finalhsn[$i][1];

$rctotal = $rctotal + $finalhsn[$i][3];

$rstotal = $rstotal + $finalhsn[$i][5];

}

$output .='
<tr>
<td>
</td>
<td>
</td>
<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>
' . $tatotal . '
</td>
<td>

</td>
<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>
'. $rctotal .'
</td>
<td>
</td>
<td align="right"> <span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>	
'. $rstotal .'
</td>
</tr>
</table>
';
$output .='

<table width="100%" cellpadding="5" cellspacing="0" style="margin-top:5px; font-family:Arial, Helvetica, sans-serif !important; font-size:12px">
<tr>
<td width="50%" style="background-color:'.$bgcolor.'">
<h3>Notes:</h3>
	'. $terms .'
</td>
<td width="50%" style="background-color:'.$bgcolor.'">
</td>
</tr>
<tr>
<td colspan="2" align="center" style="background-color:'.$bgcolor1.'; font-size:20px ">
<b>THANK YOU FOR YOUR BUSINESS!</b>
</td>
</tr>
</table>
';





//exit;


$invoiceFileName = $invname;
require 'vendor/autoload.php';

use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();



$dompdf->loadHtml(html_entity_decode($output));
$options = $dompdf->getOptions();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$dompdf->setOptions($options);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream($invoiceFileName, array("Attachment" => false));
