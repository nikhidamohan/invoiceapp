<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="style.css">
  <script>
    function applyDiscount(total) {
       var res = "success";

      let perce=0;
      var type = document.getElementsByName('percent');
      var discount = document.getElementById("discount").value;
      if(type[0].checked)
      {
        perce=(total/100)*discount;
        document.getElementById("total_after_disc").innerHTML =total-perce;
        document.getElementById("total_after_disc2").value =total-perce;
       
      }
      else if(type[1].checked)
      {
        document.getElementById("total_after_disc").innerHTML =total-discount;
        document.getElementById("total_after_disc2").value =total-discount;

      }
      
  }
  
  </script>
</head>
<body>
<!-- <button  class="btn btn-info btn-lg button" data-toggle="modal" data-target="#myModal">
Add Item</button> -->

<div class="listpage">
  <a href="action.php">
  <button  class="btn btn-info btn-lg button mb-10">Add Item</button></a>
  <table id="customers">
    <?php
    include './mysql.php';
    // Include the main TCPDF library (search for installation path).
    require_once('./tcpdf/examples/tcpdf_include.php');

    function get_percentage($total, $number)
    {
      if ( $total > 0 ) 
      {
       return round($number * ($total / 100),2);
      } 
      else 
      {
        return 0;
      }
    }

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // add a page
    $pdf->AddPage();
    $randomNumber=hash('md5', 'generate invoice');
    $pdf->Write(0, 'Invoice #', '', 0, 'L', true, 0, false, false, 0);
    $pdf->SetFont('helvetica', '', 8);

    // Create Mysql connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM items";
    $result = $conn->query($sql);
    $i=0;
    $subtotal=0;
    $subtotalWithTax=0;
    $taxamount=0;
    if ($result->num_rows > 0)
     {
          $_SESSION["isarray"]=1; 
          ?>
           <tr>
              <th>No</th>
              <th>Name</th>
              <th>Quantity</th>
              <th>Unit Price(in $)</th>
              <th>Tax(in %)</th>
              <th>Amount Total</th>
          </tr>
          <?php 
          // output data of each row
          while($row = $result->fetch_assoc()) 
          {
              $linetotal=0;
              $i++;
              $subtotal=$subtotal+($row["quantity"] * $row["price"]);
              $linetotal=$row["quantity"] * $row["price"];
              $taxamount = $taxamount+get_percentage($linetotal, $row["tax"]); 
             ?>
             <tr key=<?php echo $row["id"] ?>>
                <td><?php echo $i ?></td>
                <td><?php echo $row["name"]?></td>
                <td><?php echo $row["quantity"]?></td>
                <td><?php echo number_format($row["price"],2)?></td>
                <td><?php echo $row["tax"]?></td> 
                <td><?php echo number_format($row["quantity"] * $row["price"],2)?></td> 
              </tr>
             <?php 
            } 
          ?>
          </table>
 
          <table id="customers">
            <tr class="total">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            </tr>
          <tr class="total">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Subtotal (With tax)</th>
            <th><?php   echo number_format($subtotal+$taxamount,2);?></th>
          </tr>   
           <tr class="total">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Subtotal (Without tax)</th>
            <th><?php   
            echo number_format($subtotal,2);
            ?></th>
          </tr>  
          <tr class="total">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Apply Discount</th>
            <th>
              <input type="radio" id="percent" name="percent" value="percent" checked>
              <label for="percent">percent</label><br>   
              <input type="radio" id="amount" name="percent" value="amount">
              <label for="other">amount</label>
              <input type="number" id="discount" name="discount" value="" placeholder="percentage(%) value or an amount ($)">
              <input type="button" value="Apply" onclick="applyDiscount(<?php echo number_format($subtotal+$taxamount,2) ?>)">
            </th>
            </tr>  
           <tr class="total">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total Amount</th>
            <th id="total_after_disc"><?php  echo number_format($subtotal+$taxamount,2);?></th>
            <!-- <input type="text" id="btnClickedValue" name="btnClickedValue" value="" /> -->
            </tr>  
            <?php 
            
         
            if($_SESSION["isarray"]==1)
            {
            ?>
              <a href="index.php?pdf=1">
              <button  class="btn btn-info btn-lg button mb-10">
              Generate Invoice</button></a>
            <?php 
            } 
    } 
    else 
    {
      echo "<br/>";
      $_SESSION["isarray"]=0; 
      echo "0 items";
    }
  ?>
 
</table>

<?php
 

  $i=0;
 
  $tbl ='<table border="1" cellpadding="2" cellspacing="2" align="center">
   <tr nobr="true">
     <th>No</th>
      <th>Name</th>
      <th>Quantity</th>
      <th>Unit Price(in $)</th>
      <th>Tax(in %)</th>
      <th>Amount Total</th>
   </tr>';

 
  $sql = "SELECT * FROM items";
  $result = $conn->query($sql);
  $i=0;
  $subtotal=0;
  $subtotalWithTax=0;
  $taxamount=0;
  $finalamount=0;
  while($row = $result->fetch_assoc()) 
  {
    $linetotal=0;
    $i++;
    $subtotal=$subtotal+($row["quantity"] * $row["price"]);
    $linetotal=$row["quantity"] * $row["price"];

    $taxamount = $taxamount+get_percentage($linetotal, $row["tax"]);
    $finalamount=$subtotal+$taxamount;

     $tbl .='<tr>
        <td>'.$i.'</td>
        <td>'.$row["name"].'</td>
        <td>'.$row["quantity"].'</td>
        <td>'.number_format($row["price"],2).'</td>
        <td>'.$row["tax"].'</td> 
        <td>'.number_format($row["quantity"] * $row["price"],2).'</td> 
      </tr>';
   } 
   $tbl .='</table>
    <table>
    <tr nobr="true">
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    </tr>
    <tr class="empty"><td></td></tr>
    <tr class="empty"><td></td></tr>
    <tr class="empty"><td></td></tr>
   <tr class="total">
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th>Subtotal (With tax)</th>
    <th>'.number_format($subtotal+$taxamount,2).'</th>
    </tr>   

     <tr class="total">
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th>Subtotal (Without tax)</th>
      <th>'.number_format($subtotal,2).'</th>
    </tr>  

     <tr class="total">
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th>Total Amount</th>
      <th id="total_after_disc2">'.number_format($subtotal+$taxamount,2).'</th>
      </tr></table>';

        if($_GET["pdf"])
        {          
            $pdf->writeHTML($tbl, true, false, false, false, '');
          
            ob_end_clean();
            $pdf->Output('example_048.pdf', 'I');
            
        }
  
    $conn->close();
    ?>
</div>
</body>
</html>

