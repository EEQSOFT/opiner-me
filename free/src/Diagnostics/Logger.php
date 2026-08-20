<?php

declare(strict_types=1);

namespace OpinerMe\Diagnostics;

defined( 'ABSPATH' ) || exit;

use OpinerMe\Contracts\LoggerInterface;

class Logger implements LoggerInterface {

    private static ?Logger $instance = null;

    public static function getInstance(): Logger {
        return self::$instance ??= new self();
    }

    public static function info( string $message ): void {
        self::getInstance()->log( 'INFO', $message );
    }

    public static function warning( string $message ): void {
        self::getInstance()->log( 'WARNING', $message );
    }

    public static function error( string $message ): void {
        self::getInstance()->log( 'ERROR', $message );
    }

    public static function debug( string $message ): void {
        self::getInstance()->log( 'DEBUG', $message );
    }

    public static function critical( string $message ): void {
        self::getInstance()->log( 'CRITICAL', $message );
    }

    public static function alert( string $message ): void {
        self::getInstance()->log( 'ALERT', $message );
    }

    public static function emergency( string $message ): void {
        self::getInstance()->log( 'EMERGENCY', $message );
    }

    public static function notice( string $message ): void {
        self::getInstance()->log( 'NOTICE', $message );
    }

    public function log( string $level, string $message ): void {
        $timestamp = gmdate( 'Y-m-d H:i:s' );
        $formatted = sprintf( "[%s][%s] %s\n", $timestamp, $level, $message );

        $upload_dir = wp_upload_dir();
        $log_dir    = trailingslashit( $upload_dir['basedir'] ) . 'opiner-me/';
        $log_file   = $log_dir . 'log.txt';

        if ( ! file_exists( $log_dir ) ) {
            wp_mkdir_p( $log_dir );
        }

        file_put_contents( $log_file, $formatted, FILE_APPEND | LOCK_EX );
    }
}
