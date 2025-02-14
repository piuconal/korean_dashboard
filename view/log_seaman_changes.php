<?php
include '../db/connect.php';

function logSeamanChanges($id, $updated_by, $old_data, $new_data) {
    global $conn;

    // Danh sách các cột cần theo dõi thay đổi
    $tracked_columns = ['type', 'start_date', 'disembark_date', 'moving_fee'];

    foreach ($tracked_columns as $column) {
        $old_value = $old_data[$column] ?? null;
        $new_value = $new_data[$column] ?? null;

        if ($old_value != $new_value) {
            $history_stmt = $conn->prepare("INSERT INTO crew_history (crew_id, column_name, old_value, new_value, updated_by) 
                                            VALUES (?, ?, ?, ?, ?)");
            $history_stmt->bind_param("issss", $id, $column, $old_value, $new_value, $updated_by);
            $history_stmt->execute();
            $history_stmt->close();
        }
    }
}
?>
