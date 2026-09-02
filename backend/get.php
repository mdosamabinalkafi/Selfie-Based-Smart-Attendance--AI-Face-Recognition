<?php
include 'config.php';

header("Content-Type: application/json; charset=utf-8");
$conn->set_charset("utf8mb4");

$month = isset($_GET['month']) ? $_GET['month'] : '';

// ✅ LEFT JOIN with recover_requests to get recover status
if(!empty($month)) {
    $sql = "SELECT 
                r.*,
                rr.status as recover_status,
                rr.id as recover_id,
                rr.reviewed_at as recovered_at,
                rr.reviewed_by as recovered_by,
                CASE WHEN rr.status = 'employeeroved' THEN 1 ELSE 0 END as recovered
            FROM records r
            LEFT JOIN recover_requests rr ON r.id = rr.facility_id AND rr.status = 'employeeroved'
            WHERE r.date LIKE '$month-%' 
            ORDER BY 
                CASE WHEN rr.status = 'employeeroved' THEN 1 ELSE 0 END ASC,
                r.id DESC";
} else {
    $sql = "SELECT 
                r.*,
                rr.status as recover_status,
                rr.id as recover_id,
                rr.reviewed_at as recovered_at,
                rr.reviewed_by as recovered_by,
                CASE WHEN rr.status = 'employeeroved' THEN 1 ELSE 0 END as recovered
            FROM records r
            LEFT JOIN recover_requests rr ON r.id = rr.facility_id AND rr.status = 'employeeroved'
            ORDER BY 
                CASE WHEN rr.status = 'employeeroved' THEN 1 ELSE 0 END ASC,
                r.id DESC";
}

$result = $conn->query($sql);
$data = [];

while($row = $result->fetch_assoc()){
    $row['facility'] = getFacilityBangla($row['facility']);
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>