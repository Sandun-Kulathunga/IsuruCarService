<?php
require_once("../../database/databaseLogin.php");

session_start();
$customer_id = $_SESSION["customer_id"];


try {
    $pdo = new PDO($attr, $user, $pass, $opts);
    $query = "SELECT r.*, v.* FROM `rental` r JOIN `vehicle` v ON r.`Vehicle_Registration_number` = v.`Registration_number`
WHERE r.`Customer_ID` = :cus_id";

    $stmt = $pdo->prepare($query);
    $stmt->bindparam(":cus_id", $customer_id);
    $stmt->execute();
    $rental_vehicle = $stmt->fetchAll();
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$selected_status = $_POST['status'];


foreach ($rental_vehicle as $row) {
    static $no = 0;
    $no++;
    $status = $row['Rental_status'];

    if ($status != $selected_status) {
        $no--;
        continue;
    }

    $vehicle_name = $row['Make'] . " " . $row['Model'];
    $total_KM = $row['Total_KM'];
    $rental_date = $row['Rental_date'];
    $return_date = $row['Return_date'];
    $rental_rate = $row['Rental_rate'];


    $rentalDateTime = new DateTime($rental_date);
    $returnDateTime = new DateTime($return_date);

    $interval = $rentalDateTime->diff($returnDateTime);
    $days = $interval->days;

    if ($status == 'Completed') {

        $amount =  $row['Amount'];
        $amountlimited = number_format($amount, 2);
    } else {
        $amountlimited =  'Pending';
    }
    echo <<< _END
    
        <tr class="text-center">
            <th scope="row">$no</th>
            <td>$vehicle_name</td>
            <td scope="col">$days Days</td>
            <td>$status</td>
            <td>$amountlimited</td>
        </tr>
        _END;
}
