<!DOCTYPE HTML>
<html>

<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <?php
  include './mysql.php';
  // define variables and set to empty values
  $nameErr = $emailErr = $genderErr = $websiteErr = "";
  $name = $email = $gender = $comment = $website = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formvalid = true;
    if (empty($_POST["name"])) {
      $nameErr = "Name is required";
      $formvalid = false;
    } else {
      $name = $_POST["name"];
      // check if name only contains letters and whitespace
      if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $nameErr = "Only letters and white space allowed";
        $formvalid = false;
      }
    }
    if (empty($_POST["quantity"])) {
      $quantityErr = "quantity is required";
      $formvalid = false;
    } else {
      $quantity = $_POST["quantity"];
    }
    if (empty($_POST["price"])) {
      $priceErr = "price is required";
      $formvalid = false;
    } else {
      $price = $_POST["price"];
    }
    if ($_POST["tax"] == null) {
      $taxErr = "tax is required";
      $formvalid = false;
    } else {
      $tax = $_POST["tax"];
      $taxarray = array(0, 1, 5, 10);
      // check if name only contains letters and whitespace
      if (!in_array($tax, $taxarray)) {
        $taxErr = " should be one of these 0, 1, 5, 10";
        $formvalid = false;
      }
    }
    if ($formvalid) {

      $name =  test_input($_REQUEST['name']);
      $quantity = test_input($_REQUEST['quantity']);
      $price =  test_input($_REQUEST['price']);
      $tax = test_input($_REQUEST['tax']);

      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      $sql = "INSERT INTO items (name, quantity, price,tax)
        VALUES ('$name', '$quantity','$price','$tax')";
      if ($conn->query($sql) === TRUE) {
        echo "Item Added Successfully";
        header('Location: index.php');
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
      $conn->close();
    }
  }
  function test_input($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  ?>
  <div class="addform">
    <a href="index.php">
      <button class="btn btn-info btn-lg button">
        Back</button></a>
    <h2>Add Details</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

      <label for="name">Name :<span class="error">* <?php echo $nameErr; ?></span></label>
      <input type="text" id="name" name="name" value="<?php echo $name; ?>" placeholder="Name..">

      <br>
      <label for="name">Quantity :<span class="error">* <?php echo $quantityErr; ?></span></label>
      <input type="number" name="quantity" id="quantity" value="<?php echo $quantity; ?>" placeholder="Quantiy..">

      <br>
      <label for="price">Unit Price(in $): <span class="error">* <?php echo $priceErr; ?></span> </label>
      <input type="text" name="price" id="price" value="<?php echo $price; ?>" placeholder="Unit Price..">

      <br>
      <label for="price">Tax:(In percentage , should be one of these 0%, 1%, 5%, 10% ):<span class="error">* <?php echo $taxErr; ?></span></label>
      <input type="number" name="tax" id="tax" rows="5" cols="40" value="<?php echo $tax; ?>" placeholder="Tax..">

      <br>
      <input type="submit" name="submit" value="Submit">
    </form>
  </div>

</body>

</html>