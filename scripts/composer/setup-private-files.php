<?php

/**
 * Creates private files folder and setting.
 *
 * This is needed here, because in profile .install this generates errors.
 * Those errors are caused mainly because the default file system is set to 'private' in system.file.yml,
 * and that is imported before install scripts are run.
 * That bites and gives a faulty 'private:/' folder in web-root.
 *
 * This install script only runs during initial install,
 * so if admin changes private file folder after install, that's ok.
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
  else {
    print "Private directory created (/private).\n";
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
        sprintf('Unable to copy %s to %s', $defaultSettingsPath, $settingsPath)
    );
  }
}

// Configure Drupal's private file path.
$contents = file_get_contents($settingsPath);

$defaultSetting = "# \$settings['file_private_path'] = '';";
$privateSetting = "\$settings['file_private_path'] = '../private';";

if (strpos($contents, $privateSetting) === FALSE) {
  $contents = str_replace(
      $defaultSetting,
      $privateSetting,
      $contents
  );

  if (file_put_contents($settingsPath, $contents) === FALSE) {
    throw new RuntimeException(
        sprintf('Unable to update settings.php: %s', $settingsPath)
    );
  }
  else {
    print "Copied default.settings.php to settings.php to configure \$settings['file_private_path'] = '../private'; .\n";
  }
}


