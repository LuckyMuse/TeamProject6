<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="css/basket.css" />
</head>
<header>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="https://use.typekit.net/maf1fpm.css">
    </a>
    <section>
        <div class="topnav">
            <nav>
                <h1 class="logo">Furniche</h1>
                <ul>
                    <li><a class="active" href="Main.html">Home</a></li>
                    <li><a href="loginview.php">Login</a></li>
                    <li><a href="contactus.html">Contact Us</a></li>
                    <li><a href="About Us.html">About Us</a></li>
                </ul>
            </nav>
        </div>
    </section>

    <body>

        <h2>Your Basket</h2>

        <?php
        $dsn = "mysql:host=localhost;dbname=furniche";
        $username = "root";
        $password = "";

        $basketId = 1;

        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $stmtBasket = $pdo->prepare("
            SELECT products.productId, products.productName, products.price, products.imageName, basketproducts.quantity
            FROM basketproducts
            JOIN products ON basketproducts.productId = products.productId
            WHERE basketproducts.basketId = :user_id
            
        ");
            $stmtBasket->bindParam(':user_id', $basketId);
            $stmtBasket->execute();

            if ($stmtBasket->rowCount() > 0) {
                echo '<div class="basket-items">';
                while ($row = $stmtBasket->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="basket-item" data-productId="' . $row['productId'] . '">';
                    echo '<div class="item-image"><img src= "Furniche Products/images/' . $row['imageName'] . '" " width="250" height="300" alt="' . $row['imageName']  . '"></div>';
                    echo '<div class="item-details">';
                    echo '<p><strong>' . $row['productName'] . '</strong></p>';
                    echo '<p>Price: $' . $row['price'] . '</p>';
                    echo '<div class="quantity-controls">';
                    echo '<button onclick="adjustQuantity(' . $row['productId'] . ', -1)">-</button>';
                    echo '<span> </span><span class="quantity">' . $row['quantity'] . '</span><span> </span>';
                    echo '<button onclick="adjustQuantity(' . $row['productId'] . ', 1)">+</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<a href="product_index.php"><button>Add More Products?</button></a>';

            } else {
                echo "<p>Your basket is empty.</p>";
                echo '<a href="product_index.php"><button>Add Products?</button></a>';

            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        echo "<br>";
        $pdo = null;
        include 'connectdb.php';
        $basket_id = 1;
        $sql = "SELECT price, quantity FROM products JOIN basketproducts ON products.productId = basketproducts.productId WHERE basketId = $basket_id";
        $result = $conn->query($sql);
        $basketcost = 0;
        if ($result->rowCount() > 0) {
            while ($row = $result->fetch()) {
                $basketcost = $basketcost + $row["quantity"] * $row["price"];
            }
            echo "£" . $basketcost . " before discount</br>";
        } else {
            echo "0 results";
        }



        $discount_name = "Discount 1"; #$discount_name = $_POST['discount'];
        $sql = "SELECT value FROM discounts WHERE discountTitle = '" . $discount_name . "'";
        $value = $conn->query($sql);
        $basketcost = $basketcost * (1 - $value->fetch()["value"] / 100);
        echo "£" . $basketcost . " total</br>";


        #stock availability check
        function availability($conn, $basket_id)
        {
            $available = true;
            $sql = "SELECT productName, countStock, quantity FROM products join basketproducts ON products.productId = basketproducts.productId  WHERE basketId = $basket_id";
            $result = $conn->query($sql);
            if ($result->rowCount() > 0) {
                while ($row = $result->fetch()) {
                    if ($row["quantity"] > $row["countStock"]) {
                        echo $row["productName"] . " is unavailable </br>";
                        $available = false;
                    }
                }
            }
            return $available;
        }
        if (availability($conn, $basket_id)) {
            echo "available";
        }
        ?>

        <script>
            function adjustQuantity(productId, change) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'basket_quantity.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var quantityElement = document.querySelector('.basket-item[data-productId="' + productId + '"] .quantity');
                        var newQuantity = parseInt(quantityElement.textContent) + change;

                        quantityElement.textContent = newQuantity;

                        if (newQuantity === 0) {
                            var basketItem = document.querySelector('.basket-item[data-productId="' + productId + '"]');
                            if (basketItem) {
                                basketItem.remove();
                            }
                        }
                    }
                };
                xhr.send('product_id=' + productId + '&change=' + change);
            }
        </script>

    </body>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h4>About Us</h4>
                    <ul>
                        <li><a href="#">Our Founder</a> </li>
                        <li><a href="#">Our Values</a> </li>
                        <li><a href="#">Our Privacy Policy</a> </li>
                        <li><a href="#">Our Services</a> </li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Address</h4>
                    <h5>206 Canada Place, Liverpool Street, E12 1CL</h5>
                </div>
                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <h5>Email us at: comms@furniche.com</h5>
                    <h5>Call us at: 01563385967</h5>
                    <ul>
                        <li><a href="contact.html">Contact Us via our Website</a> </li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Follow us</h4>
                    <div class="social-links">
                        <a href="https://en-gb.facebook.com/"><i class="fab fa-facebook - f"></i></a>
                        <a href="https://twitter.com/?lang=en"><i class="fab fa-twitter"></i></a>
                        <a href="https://uk.linkedin.com/"><i class="fab fa-linkedin - in"></i></a>
                        <a href="https://github.com/"><i class="fab fa-github"></i></a>
                        <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</html>