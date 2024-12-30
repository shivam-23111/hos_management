<?php
require 'db_connection.php';

// Example: Load your AI model and required libraries
// Assuming you have a Python script that returns predictions via command line
// You can also use an API if the model is hosted elsewhere

function predict_patient_schedule($patient_data) {
    // Command to run your AI model script with patient data as input
    // Adjust the command to match your environment and model
    $command = escapeshellcmd('python3 predict_schedule.py ' . escapeshellarg(json_encode($patient_data)));
    $output = shell_exec($command);
    return json_decode($output, true); // Return predicted arrival and duration
}

// Fetch the patients for whom predictions need to be made
$query = $conn->query("
    SELECT id, name, age, gender, problem
    FROM opd_registrations
    WHERE predicted_arrival_time IS NULL
");

$patients = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($patients as $patient) {
    // Example patient data for prediction
    $patient_data = [
        'age' => $patient['age'],
        'gender' => $patient['gender'],
        'problem' => $patient['problem']
    ];

    // Get predictions
    $predictions = predict_patient_schedule($patient_data);

    // Store predictions in the database
    $update_query = $conn->prepare("
        UPDATE opd_registrations
        SET predicted_arrival_time = ?, predicted_duration = ?
        WHERE id = ?
    ");
    $update_query->execute([
        $predictions['arrival_time'],
        $predictions['duration'],
        $patient['id']
    ]);
}

echo "Predictions updated successfully.";
?>
