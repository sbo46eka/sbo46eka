<?php
// header('Content-Type: application/json');
// session_start();
// require('../config.php');

// $rawData = file_get_contents('php://input');
// $data = json_decode($rawData, true);

// // Check if contracts data is valid
// if (isset($data['contracts']) && is_array($data['contracts'])) {
//     try {
        
        
//         // Prepare the insert statement
//         $stmt = $conn->prepare("
//             INSERT INTO contract (
//                 `ref_number`, `contract_number`, `loan_purpose`, `loan_amount`, `credit_advance`, 
//                 `repayment_period`, `next_pay_date`, `last_pay_date`, `first_installment`, `monthly_instalment`, 
//                 `total_repayment`, `hear_about_us`, `client_signature`, `witness_signature`, 
//                 `contract_status`, `track_status`, `attend_status`, `client_existance`, `done_at_branch`
//             ) VALUES (
//                 :refNumber, :contractNumber, :loanPurpose, :loanAmount, :advanceCredit, 
//                 :repaymentPeriod, :nextPayDate, :lastPayDate, :firstMonthInstallment, :monthlyInstallment, 
//                 :totalRepayment, :hearAboutUs, :clientSignature, :witnessSignature, 
//                 :contractStatus, :trackStatus, :attendStatus, :clientExistance, :doneAtBranch
//             )
//         ");

//         foreach ($data['contracts'] as $contract) {
//             // Set variables with fallback to empty strings
//             $stmt->execute([
//                 ':refNumber' => $contract['refNumber'] ?? '',
//                 ':contractNumber' => $contract['contractNumber'] ?? '',
//                 ':loanPurpose' => $contract['loanPurpose'] ?? '',
//                 ':loanAmount' => $contract['loanAmount'] ?? '',
//                 ':advanceCredit' => $contract['advanceCredit'] ?? '',
//                 ':repaymentPeriod' => $contract['contractType'] ?? '',
//                 ':nextPayDate' => $contract['nextPayDate'] ?? '',
//                 ':lastPayDate' => $contract['lastPayDate'] ?? '',
//                 ':firstMonthInstallment' => isset($contract['firstMonthInstallment']) 
//                     ? number_format((float)$contract['firstMonthInstallment'], 2, '.', '') 
//                     : '',
//                 ':monthlyInstallment' => isset($contract['monthlyInstallment']) 
//                     ? number_format((float)$contract['monthlyInstallment'], 2, '.', '') 
//                     : '',
//                 ':totalRepayment' => isset($contract['totalRepayment']) 
//                     ? number_format((float)$contract['totalRepayment'], 2, '.', '') 
//                     : '',
//                 ':hearAboutUs' => $contract['hearAboutUs'] ?? '',
//                 ':clientSignature' => $contract['clientSignature'] ?? '',
//                 ':witnessSignature' => $contract['witnessSignature'] ?? '',
//                 ':contractStatus' => 'Waiting',
//                 ':trackStatus' => 'Not Assigned',
//                 ':attendStatus' => 'Not Attended',
//                 ':clientExistance' => $contract['clientExistance'] ?? '',
//                 ':doneAtBranch' => $contract['doneAtBranch'] ?? ''
//             ]);
//         }

//         echo json_encode(['status' => 'success', 'message' => 'Contracts saved successfully']);
//     } catch (Exception $e) {
//         echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
//     }
// } else {
//     echo json_encode(['status' => 'error', 'message' => 'Invalid contract data received']);
// }
?>

<?php
header('Content-Type: application/json');
session_start();
require('../config.php');

try {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    // Validate contracts data
    if (!isset($data['contracts']) || !is_array($data['contracts'])) {
        throw new Exception('Invalid contract data received.');
    }

    // Retrieve session variables
    $f_name = $_SESSION['f_name'] ?? '';
    $contract_number = $_SESSION['contract'] ?? '';
    $branch = $_SESSION['branch'] ?? '';
    $client_existance = $_SESSION['client_existance'] ?? '';

    

    

    // Iterate through contracts and execute the statement
    foreach ($data['contracts'] as $contract) {
        // Get the current count of contracts
        $sql = "SELECT COUNT(*) as count FROM contract";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = (int) $row['count'];
    
        // Generate reference number
        if ($count < 10) {
            $ref_number = 'FUTRE#0000' . $count;
        } elseif ($count < 100) {
            $ref_number = 'FUTRE#000' . $count;
        } elseif ($count < 1000) {
            $ref_number = 'FUTRE#00' . $count;
        } elseif ($count < 10000) {
            $ref_number = 'FUTRE#0' . $count;
        } else {
            $ref_number = 'FUTRE#' . $count;
        }
    
        // Initialize the session key as an array if it doesn't exist
        if (!isset($_SESSION["ref_number"])) {
            $_SESSION["ref_number"] = [];
        }
    
        // Append the new ref_number to the session array
        $_SESSION["ref_number"][] = $ref_number;
        
        // Prepare the SQL insert statement
        $stmt = $conn->prepare("
            INSERT INTO contract (
                    `ref_number`, `contract_number`, `loan_purpose`, `loan_amount`, `credit_advance`, 
                    `repayment_period`, `next_pay_date`, `last_pay_date`, `first_installment`, `monthly_instalment`, 
                    `total_repayment`, `hear_about_us`, `client_signature`, `witness_signature`, 
                    `contract_status`, `track_status`, `attend_status`, `client_existance`, `done_at_branch`
                ) VALUES (
                    :refNumber, :contractNumber, :loanPurpose, :loanAmount, :advanceCredit, 
                    :repaymentPeriod, :nextPayDate, :lastPayDate, :firstMonthInstallment, :monthlyInstallment, 
                    :totalRepayment, :hearAboutUs, :clientSignature, :witnessSignature, 
                    :contractStatus, :trackStatus, :attendStatus, :clientExistance, :doneAtBranch
                )
        ");
    
        $stmt->execute([
            ':refNumber' => $ref_number,
            ':contractNumber' => $contract_number,
            ':loanPurpose' => $contract['loanPurpose'] ?? '',
            ':loanAmount' => $contract['loanAmount'] ?? '',
            ':advanceCredit' => $contract['advanceCredit'] ?? '',
            ':repaymentPeriod' => $contract['contractType'] ?? '',
            ':nextPayDate' => $contract['nextPayDate'] ?? '',
            ':lastPayDate' => $contract['lastPayDate'] ?? '',
            ':firstMonthInstallment' => isset($contract['firstMonthInstallment']) 
                ? number_format((float)$contract['firstMonthInstallment'], 2, '.', '') 
                : '0.00',
            ':monthlyInstallment' => isset($contract['monthlyInstallment']) 
                ? number_format((float)$contract['monthlyInstallment'], 2, '.', '') 
                : '0.00',
            ':totalRepayment' => isset($contract['totalRepayment']) 
                ? number_format((float)$contract['totalRepayment'], 2, '.', '') 
                : '0.00',
            ':hearAboutUs' => $contract['hearAboutUs'] ?? '',
            ':clientSignature' => $contract['clientSignature'] ?? '',
            ':witnessSignature' => $contract['witnessSignature'] ?? '',
            ':contractStatus' => 'Waiting',
            ':trackStatus' => 'Not Assigned',
            ':attendStatus' => 'Not Attended',
            ':clientExistance' => $client_existance,
            ':doneAtBranch' => $branch
        ]);
    }

    // Send success response
    echo json_encode(['status' => 'success', 'message' => 'Contracts saved successfully']);
} catch (Exception $e) {
    // Send error response
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>

