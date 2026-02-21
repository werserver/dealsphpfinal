<?php
/**
 * CSV Management API Endpoints
 */

function handleCsv() {
    if (isGet()) {
        handleGetCsv();
    } elseif (isPost()) {
        Auth::requireLogin();
        handleUploadCsv();
    } elseif (isDelete()) {
        Auth::requireLogin();
        handleDeleteCsv();
    } else {
        sendError('Method not allowed', 405);
    }
}

function handleGetCsv() {
    $category = sanitizeInput(getQueryParam('category', ''));
    
    if (empty($category)) {
        // Return list of all CSV files
        $files = CsvParser::getCsvFiles();
        sendSuccess(['categories' => $files]);
    } else {
        // Return CSV content for specific category
        $csv_path = STORAGE_PATH . '/csv/' . sanitizeFilename($category) . '.csv';
        
        if (file_exists($csv_path)) {
            $content = file_get_contents($csv_path);
            sendSuccess(['data' => $content, 'category' => $category]);
        } else {
            sendError('CSV file not found', 404);
        }
    }
}

function handleUploadCsv() {
    $category = sanitizeInput(getQueryParam('category', ''));
    $input = getJsonInput();
    $csvContent = $input['csvContent'] ?? '';

    if (empty($category)) {
        return sendError('Category is required');
    }

    if (empty($csvContent)) {
        return sendError('CSV content is required');
    }

    // Save CSV file
    CsvParser::saveCsvToFile($category, $csvContent);

    sendSuccess([
        'category' => $category,
        'fileName' => sanitizeFilename($category) . '.csv'
    ], 'CSV uploaded successfully');
}

function handleDeleteCsv() {
    $category = sanitizeInput(getQueryParam('category', ''));

    if (empty($category)) {
        return sendError('Category is required');
    }

    if (CsvParser::deleteCsvFile($category)) {
        sendSuccess(null, 'CSV deleted successfully');
    } else {
        sendError('CSV file not found', 404);
    }
}
?>
