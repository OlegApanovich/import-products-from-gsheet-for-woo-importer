<?php
/**
 * Service class help to interact with Google sheet API.
 *
 * @since 2.0.0
 *
 * @package GSWOO\Services
 */

namespace GSWOO\Services;

use Google\Service\Sheets;
use Google\Service\Drive;
use GSWOO\Abstracts\GoogleApiInterplayAbstract;
use WP_Error;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Class SheetInterplayService
 *
 * @since  2.0.0
 *
 * @package GSWOO\Services
 */
class SheetInterplayService extends GoogleApiInterplayAbstract {

    /**
     * Instance of Google_Service_Sheets class.
     *
     * @since  2.0.0
     * @var object Google\Service\Sheets\Google_Service_Sheets
     */
    public $google_service_sheets;

    /**
     * Instance of Google_Service_Drive class.
     *
     * @since  2.4
     * @var object Google\Service\Drive\Google_Service_Drive
     */
    public $google_service_drive;

    /**
     * SheetInterplayService constructor.
     *
     * @param array $options
     */
    public function __construct( $options ) {

        if ( empty( $options['google_auth_type'] ) ) {
            return;
        }

        $this->options = $options;
        $token_service = $this->get_token_service();

        try {
            $this->google_service_sheets = new Sheets( $token_service->client );
            $this->google_service_drive  = new Drive( $token_service->client );
        } catch ( Exception $e ) {
            $this->error = new WP_Error(
                'api_connect_error',
                '(' . __METHOD__ . ') ' . $e->getMessage()
            );
        }
    }

    /**
     * Get sheet csv data.
     *
     * @since 2.0.0
     *
     * @param string $sheet_id
     *
     * @return WP_Error|array
     */
    public function get_sheet_csv( $sheet_id ) {
        if ( $this->error ) {
            return $this->error;
        }

        try {
            $file_meta = $this->google_service_drive->files->get(
                $sheet_id,
                array( 'fields' => 'mimeType' )
            );
            $mime_type = $file_meta->getMimeType();

        } catch ( Exception $e ) {
            return new WP_Error(
                'get_sheet_csv',
                '(' . __METHOD__ . ') ' . $e->getMessage()
            );
        }

        if ( 'application/vnd.google-apps.spreadsheet' === $mime_type ) {
            return $this->get_sheet_csv_via_sheets_api( $sheet_id );
        }

        return $this->get_sheet_csv_from_drive_file( $sheet_id );
    }

    /**
     * Fetch sheet data via Google Sheets API (native Google Sheets files only).
     *
     * @since 2.4
     *
     * @param string $sheet_id
     *
     * @return WP_Error|array
     */
    private function get_sheet_csv_via_sheets_api( $sheet_id ) {
        try {
            $spreadsheet = $this->google_service_sheets->spreadsheets->get( $sheet_id );

            $sheet_name = $spreadsheet[0]->properties->title;

            $sheet =
                $this->google_service_sheets->
                spreadsheets_values->get( $sheet_id, $sheet_name );
        } catch ( Exception $e ) {
            return new WP_Error(
                'get_sheet_csv',
                '(' . __METHOD__ . ') ' . $e->getMessage()
            );
        }

        if ( empty( $sheet->values ) ) {
            return new WP_Error(
                'get_sheet_csv',
                __(
                    "We can't receive any data from your google sheet, please check if your spread sheet is not empty",
                    'import-products-from-gsheet-for-woo-importer'
                )
            );
        }

        return $sheet->values;
    }

    /**
     * Download a Drive file and parse it into a 2D array via PhpSpreadsheet.
     *
     * @since 2.0.0
     *
     * @param string $sheet_id
     *
     * @return WP_Error|array
     */
    private function get_sheet_csv_from_drive_file( $sheet_id ) {
        try {
            $response = $this->google_service_drive->files->get(
                $sheet_id,
                array( 'alt' => 'media' )
            );
            $content  = $response->getBody()->getContents();
        } catch ( Exception $e ) {
            return new WP_Error(
                'get_sheet_csv',
                '(' . __METHOD__ . ') ' . $e->getMessage()
            );
        }

        $tmp = tempnam( sys_get_temp_dir(), 'gswoo_' );
        file_put_contents( $tmp, $content );

        return $this->parse_spreadsheet_file( $tmp );
    }

    /**
     * Load a local spreadsheet file via PhpSpreadsheet and return its data as a 2D array.
     *
     * @since 2.4
     *
     * @param string $file_path  Absolute path to the spreadsheet file.
     *
     * @return WP_Error|array
     */
    private function parse_spreadsheet_file( $file_path ) {
        try {
            $spreadsheet = IOFactory::load( $file_path );
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray( '', false );
        } catch ( Exception $e ) {
            unlink( $file_path );
            return new WP_Error(
                'get_sheet_csv',
                '(' . __METHOD__ . ') ' . $e->getMessage()
            );
        }

        unlink( $file_path );

        $rows = $this->drop_trailing_empty_rows( $rows );

        if ( empty( $rows ) ) {
            return new WP_Error(
                'get_sheet_csv',
                __(
                    "We can't receive any data from your google sheet, please check if your spread sheet is not empty",
                    'import-products-from-gsheet-for-woo-importer'
                )
            );
        }

        return $rows;
    }

    /**
     * Remove trailing rows where every cell is null or an empty string.
     *
     * @since 2.4
     *
     * @param array $rows
     *
     * @return array
     */
    private function drop_trailing_empty_rows( array $rows ) {
        while ( ! empty( $rows ) && $this->is_empty_row( end( $rows ) ) ) {
            array_pop( $rows );
        }

        return $rows;
    }

    /**
     * Check whether a spreadsheet row contains no meaningful data.
     *
     * A row is considered empty when every cell is either null or an empty
     * string, which is the placeholder PhpSpreadsheet uses for unset cells.
     *
     * @since 2.4
     *
     * @param array $row  Flat array of cell values for a single row.
     *
     * @return bool True if every cell is null or empty string, false otherwise.
     */
    private function is_empty_row( array $row ) {
        return empty( array_filter( $row, fn( $v ) => null !== $v && '' !== $v ) );
    }
}
