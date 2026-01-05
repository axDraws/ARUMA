<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/Model/TherapistModel.php';

echo "=== Testing TherapistModel ===\n";

$model = new TherapistModel();

// 1. Create
echo "1. Creating Therapist...\n";
$data = [
    'nombre' => 'Test Terapeuta',
    'especialidad' => 'Test Specialty',
    'telefono' => '1234567890'
];
$result = $model->create($data);
echo "Create Result: " . ($result ? "Success" : "Failed") . "\n";

// 2. Read (All)
echo "2. Listing Therapists...\n";
$all = $model->getAllTherapists();
$testId = null;
foreach ($all as $t) {
    if ($t['nombre'] === 'Test Terapeuta') {
        $testId = $t['id'];
        echo "Found Test Therapist with ID: $testId\n";
    }
}

if (!$testId) {
    echo "Error: Could not find created therapist.\n";
    exit;
}

// 3. Update
echo "3. Updating Therapist ID $testId...\n";
$updateData = [
    'nombre' => 'Test Terapeuta Updated',
    'especialidad' => 'Updated Specialty',
    'telefono' => '0987654321'
];
$result = $model->update($testId, $updateData);
echo "Update Result: " . ($result ? "Success" : "Failed") . "\n";

// Verify Update
$updated = $model->getTherapistById($testId);
echo "Updated Name: " . $updated['nombre'] . "\n";

// 4. Delete
echo "4. Deleting Therapist ID $testId...\n";
$result = $model->delete($testId);
echo "Delete Result: " . ($result ? "Success" : "Failed") . "\n";

// Verify Delete
$check = $model->getTherapistById($testId);
// Soft delete => activo = 0
if ($check && $check['activo'] == 0) {
    echo "Verification: Therapist is inactive (Soft Deleted).\n";
} else {
    echo "Verification Error: Therapist still active or not found.\n";
}

echo "=== Test Complete ===\n";
