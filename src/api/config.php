<?php
/**
 * Configuration API Endpoints
 */

function handleConfig() {
    if (isGet()) {
        $config = Config::get();
        sendSuccess($config);
    } elseif (isPost()) {
        Auth::requireLogin();
        
        $input = getJsonInput();
        
        // Update config
        foreach ($input as $key => $value) {
            Config::set($key, $value);
        }
        
        Config::save();
        sendSuccess(Config::get(), 'Configuration saved successfully');
    } else {
        sendError('Method not allowed', 405);
    }
}
?>
