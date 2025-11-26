<?php declare(strict_types=1);

# DATENBANKEN:
$database_prefix = 'htn_server';
$database_suffix = '';

# Explicit connection details for MariaDB/MySQL.
$db_use_this_values = true;
$db_host = getenv('HTN_DB_HOST') ?: '127.0.0.1';
$db_port = (int) (getenv('HTN_DB_PORT') ?: 3306);
$db_username = getenv('HTN_DB_USER') ?: 'root';
$db_password = getenv('HTN_DB_PASSWORD') ?: '';
$db_charset = 'utf8mb4';

# STYLESHEETS:
$standard_stylesheet = 'crystal';
$stylesheets['standard'] = array(
    'id' => 'standard',
    'name' => 'HackTheNet Standard',
    'author' => 'HackTheNet-Team',
    'bigacc' => 'no',
);
$stylesheets['crystal'] = array(
    'id' => 'crystal',
    'name' => 'HackTheNet Crystal',
    'author' => 'HackTheNet-Team',
    'bigacc' => 'no',
);
$stylesheets['konsole'] = array('id' => 'konsole', 'name' => 'Konsole', 'author' => 'Volkmar', 'bigacc' => 'yes');
$stylesheets['anti-ie'] = array('id' => 'anti-ie', 'name' => 'Anti-IE', 'author' => 'Volkmar', 'bigacc' => 'yes');

# KEINE MITSPIELER:
$no_ranking_users = '1,2';
$no_ranking_clusters = '2'; # Nur eine Angabe möglich

# KONSTANTEN
define('UPDATE_INTERVAL', 10800); # Interval für Punkte-Updates in Sekunden
define('MIN_ATTACK_XDEFENCE', 9);
define('MIN_INACTIVE_TIME', 259200); # Inaktive Zeit vor möglichem Angriff
define('REMOTE_HIJACK_DELAY', 172800); # Wartezeit zwischen zwei Remote Hijacks
define('MAX_USERS_PER_SERVER', 4444); # Maximale Anzahl von Spielern pro Server

# DIVERSES
$REMOTE_FILES_DIR = '.'; # dreck
$STYLESHEET_BASEDIR = 'styles/';


?>
