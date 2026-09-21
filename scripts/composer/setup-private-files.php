<?php

/**
 * @file
 * Creates private files folder and setting.
 *
 * This is needed here, because in profile .install this generates errors.
 * Those errors are caused mainly because the default file system is set to 'private' in system.file.yml,
 * and that is imported before install scripts are run.
 * That bites and gives a faulty 'private:/' folder in web-root after install is completed.
 *
 * This install script underneath only runs during initial 'composer install',
 * so if admin changes private file folder after install, that's ok.
 *
 * DDEV will handle this correctly:
 * - 'ddev config' áfter 'composer install': settings.php already exists for ddev,
 *   ddev will append her needed config on the bottom of the file.
 * - 'ddev config' befóre 'composer install': settings.php already exists for OpenLucius,
 *   script underneath will her needed config on the bottom of the file.
 */

$projectRoot = dirname(__DIR__, 2);

$privatePath = $projectRoot . '/private';
$settingsDir = $projectRoot . '/web/sites/default';
$defaultSettingsPath = $settingsDir . '/default.settings.php';
$settingsPath = $settingsDir . '/settings.php';

// Create private directory.
if (!is_dir($privatePath)) {
  if (!mkdir($privatePath, 0770, TRUE)) {
    throw new RuntimeException(
        sprintf('Unable to create private directory: %s', $privatePath)
    );
  }
}

// Create settings.php from default.settings.php if it doesn't exist.
if (!file_exists($settingsPath)) {
  if (!file_exists($defaultSettingsPath)) {
    throw new RuntimeException(
        sprintf('Unable to find default.settings.php: %s', $defaultSettingsPath)
    );
  }

  if (!copy($defaultSettingsPath, $settingsPath)) {
    throw new RuntimeException(
        sprintf(
            'Unable to copy %s to %s',
            $defaultSettingsPath,
            $settingsPath
        )
    );
  }
}

// Add private file path to settings.php if it isn't already present.
$contents = file_get_contents($settingsPath);
$privateSetting = "\$settings['file_private_path'] = '../private';";
$contents = rtrim($contents) . "\n\n" . $privateSetting . "\n";

if (file_put_contents($settingsPath, $contents) === FALSE) {
  throw new RuntimeException(
      sprintf('Unable to update settings.php: %s', $settingsPath)
  );
}

echo "Private files directory and settings.php configured successfully.\n";

