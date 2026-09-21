<?php

include('header.php');
//$invoice->checkLoggedIn();
/* if (!empty($_POST['companyName']) && $_POST['companyName']) {
   $invoice->saveInvoice($_POST);
   header("Location:invoice_list.php");
} */
$cid = 0;
if (isset($_GET['id'])) {
   $cid = $_GET['id'];
} else {
   echo "<script>alert('Please select Company');</script>";
   echo "<script>window.location = '../index.html#popup';</script>";
}
//echo $cid;
$logo = "";
$name = "";
$address = "";
$email = "";
$contact = "";
$gstin = "";
$state = "";
$statecode = "";
$bpclcode = "";
$billedto = "";
$placeofsuplly = "";
$vgstin = "";
$vaddress = "";
$bankname = "";
$branch = "";
$accno = "";
$ifsc = "";
$terms = "";

if ($cid == 2) {
   $logo = "images/os-logo-small.png";
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
   $color = "#e19219";
} else if ($cid == 1) {
   $logo = "images/fe-logo-small.png";
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
   $color = "#40289f";
}



?>
<title>Invoice System</title>
<script src="js/invoice.js"></script>
<link href="css/style.css" rel="stylesheet">
<?php //include('container.php');
?>
<div class="container content-invoice" style="color: <?php echo $color; ?> !important;">


   <div class="cards">
      <div class="card-bodys">
         <form action="view-data.php" id="invoice-form" method="POST" class="invoice-form" role="form" novalidate="">
            <div class="load-animate animated fadeInUp">
               <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                     <h2 class="title" style="text-align: center;">INVOICE</h2>
                     <?php //include('menu.php'); 
                     ?>
                  </div>
               </div>
               <input id="currency" name="currency" type="hidden" value="&#8377;">
               <input id="comp" name="comp" type="hidden" value="<?php echo $cid; ?>">
               <div class="row">
                  <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                     <?php if ($logo) { ?>

                        

                        <img src="<?php echo $logo; ?>">
                     <?php } ?>
                  </div>
                  <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7">

                     <h1><?php echo $name; ?></h1>
                     <h4><?php echo $address;  ?></h4>
                     <h5>Phone No.: <?php echo $contact;  ?></h5>
                     <h5>Email : <?php echo $email;  ?></h5>
                     <h4>GSTIN : <?php echo $gstin;  ?></h4>
                     <h6>State : <?php echo $state;  ?> Code : <?php echo $statecode;  ?></h6>

                  </div>
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                     <h6>Invoice No.</h6>
                     <input type="text" name="invno" id="invno" class="form-control" autocomplete="off">

                     <h6>Invoice Date</h6>
                     <input type="date" name="invdt" id="invdt" class="form-control" autocomplete="off">

                     <h6>PO</h6>
                     <input type="text" name="pono" id="pono" class="form-control" autocomplete="off">

                     <h6>BPCL Vendor Code</h6>
                     <input type="text" readonly name="bpclcode" id="bpclcode" value="<?php echo $bpclcode; ?>" class="form-control" autocomplete="off">

                  </div>
               </div>
               <div class="row" style="border-top: 1px solid #000; background-color: rgb(177, 177, 180);">
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                     <h5>Bill To </h5>

                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                     <h5>Address</h5>
                  </div>

               </div>
               <div class="row" style="border-top: 1px solid #000; border-bottom: 1px solid #000; background-color:rgb(201, 201, 202);">
                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                     <h5><?php echo $billedto; ?> </h5>

                     <h5><?php echo $placeofsuplly . "<br>GSTIN: " . $vgstin;  ?> </h5>

                  </div>
                  <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                     <h5>&nbsp;</h5>

                  </div>

                  <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                     <h5><?php echo $vaddress; ?></h5>
                  </div>

               </div>
               <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="height: 5px;">

                  </div>
               </div>

               <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                     <table class="table table-condensed table-striped" id="invoiceItem">
                        <tr>
                           <th width="2%">
                              <div class="custom-control custom-checkbox mb-3">
                                 <input type="checkbox" class="custom-control-input" id="checkAll" name="checkAll">
                                 <label class="custom-control-label" for="checkAll"></label>
                              </div>
                           </th>
                           <th>Sr. N</th>
                           <th width="20%">Item Description</th>
                           <th>HSN/SAC</th>
                           <th>SOR</th>
                           <th>Qty</th>
                           <th>Rate</th>
                           <th>Unit</th>
                           <th>Taxable Value</th>
                           <th>GST %</th>
                           <th>GST Amt</th>
                           <th>Line Total</th>
                        </tr>
                        <tr>
                           <td>
                              <div class="custom-control custom-checkbox">
                                 <input type="checkbox" class="itemRow custom-control-input" id="itemRow_1">
                                 <label class="custom-control-label" for="itemRow_1"></label>
                              </div>
                           </td>
                           <td><input type="text" name="srno[]" id="srno_1" class="form-control" autocomplete="off"></td>
                           <td><input type="text" name="itemdesc[]" id="itemdesc_1" class="form-control" autocomplete="off"></td>
                           <td><input type="number" name="hsnsac[]" id="hsnsac_1" class="form-control quantity" autocomplete="off"></td>
                           <td><input type="number" name="sor[]" id="sor_1" class="form-control quantity" autocomplete="off"></td>
                           <td><input type="number" name="quantity[]" id="quantity_1" class="form-control quantity" autocomplete="off"></td>
                           <td><input type="number" name="price[]" id="price_1" class="form-control price" autocomplete="off"></td>
                           <td><input type="text" name="unit[]" id="unit_1" class="form-control" autocomplete="off"></td>
                           <td><input type="number" name="total[]" id="total_1" class="form-control total" autocomplete="off"></td>
                           <td><input value="" type="number" class="form-control" name="taxRate[]" id="taxRate_1" placeholder="Tax Rate"></td>
                           <td><input type="number" name="gst_amt[]" id="gst_amt_1" class="form-control total" autocomplete="off"></td>
                           <td><input type="number" name="ftotal[]" id="ftotal_1" class="form-control total" autocomplete="off"></td>
                        </tr>

                     </table>
                     <div class="row">
                        <div class="col-xs-12">
                           <button class="btn btn-danger delete" id="removeRows" type="button"> - </button>
                           <button class="btn btn-success" id="addRows" type="button"> + </button>
                        </div>
                     </div>
                     <p>&nbsp;</p>
                     <table class="table" style="border-bottom: 1px solid #000; background-color:rgb(201, 201, 202);">
                        <tr>
                           <td colspan="6" rowspan="3" style="width: 50% !important; padding: 5px !important;">
                              <h4>Bank Details</h4>
                              <p>Bank Name: <?php echo $bankname; ?></p>
                              <p>Branch Name: <?php echo $branch; ?></p>
                              <p>Account No: <?php echo $accno; ?></p>
                              <p>IFSC Code: <?php echo $ifsc; ?></p>

                           </td>
                           <td colspan="2">
                              Total Amount
                           </td>
                           <td>


                              <input value="" type="number" class="form-control" name="subTotal" id="subTotal" placeholder="Subtotal">

                           </td>
                           <td>
                              &nbsp;
                           </td>
                           <td>


                              <input value="" type="number" class="form-control" name="taxAmount" id="taxAmount" placeholder="Tax Amount">

                           </td>
                           <td>

                              <input value="" type="number" class="form-control" name="totalAftertax" id="totalAftertax" placeholder="Total">
                           </td>
                        </tr>
                        <tr>
                           <td colspan="3">
                              Round Off
                           </td>
                           <td colspan="3">
                              <input value="" type="number" class="form-control" name="roundoff" id="roundoff" placeholder="Round Off">
                           </td>
                        </tr>
                        <tr>
                           <td colspan="3">
                              Final Amount
                           </td>
                           <td colspan="3">
                              <input value="" type="number" class="form-control" name="finalamount" id="finalamount" placeholder="Final Total">
                           </td>
                        </tr>
                     </table>
                  </div>
               </div>
               <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                     <table class="table" border="1">
                        <tr>
                           <td>
                              Sr. No.
                           </td>
                           <td>
                              HSN/SAC NO
                           </td>
                           <td>
                              Taxable Value
                           </td>
                           <td colspan="2">
                              Central Tax (CGST)
                           </td>
                           <td colspan="2">
                              State Tax (SGST)
                           </td>
                        </tr>
                        <tr>
                           <td>

                           </td>
                           <td>

                           </td>
                           <td>

                           </td>
                           <td>
                              Rate
                           </td>
                           <td>
                              Amount
                           </td>
                           <td>
                              Rate
                           </td>
                           <td>
                              Amount
                           </td>
                        </tr>
                        <tr>
                           <td>

                           </td>
                           <td>

                           </td>
                           <td>

                           </td>
                           <td>

                           </td>
                           <td>

                           </td>
                           <td>

                           </td>
                           <td>

                           </td>
                        </tr>
                     </table>

                  </div>
               </div>

               <div class="row">

                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                     <h3>Notes: </h3>
                     <div class="form-group">
                        <?php echo $terms; ?>
                     </div>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">


                  </div>

                  <div class="form-group">

                     <input data-loading-text="Saving Invoice..." type="submit" name="invoice_btn" value="Save Invoice" class="btn btn-success submit_btn invoice-save-btm">
                  </div>
               </div>
               <div class="clearfix"></div>
            </div>
         </form>
      </div>
   </div>
</div>
</div>
<?php include('footer.php'); ?>