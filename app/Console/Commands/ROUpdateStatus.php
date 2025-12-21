<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use App\Helpers\RajaOngkir;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ROUpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ro-update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Update Status Raja Ongkir';

    private const BATCH_SIZE = 20;
    private const DELIVERY_STATUS_DELIVERED = 'DELIVERED';
    private const LETTER_STATUS_DELIVERED = 'TERKIRIM';
    private const LETTER_STATUS_IN_DELIVERY = 'DALAM PENGIRIMAN';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $letters = $this->fetchLettersForTracking();

            if (empty($letters)) {
                $this->info('No letters to process');

                return 0;
            }

            $this->info('Processing ' . count($letters) . ' letters...');

            $processed = 0;
            $updated = 0;

            foreach ($letters as $letter) {
                $processed++;

                if ($this->updateLetterStatus($letter)) {
                    $updated++;
                }

                if ($processed % 5 === 0) {
                    $this->info("Processed: {$processed}/" . count($letters));
                }
            }

            $this->info("Complete! Updated: {$updated}/{$processed}");

            return 0;
        } catch (\Exception $e) {
            Log::error('Letter status update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('Failed to update letter status: ' . $e->getMessage());

            return 1;
        }
    }

    /**
     * fetchLettersForTracking
     *
     * @return array
     */
    private function fetchLettersForTracking()
    {
        $query = "
            SELECT
                letter.letter_id,
                letter.receipt_no,
                jasa_pengiriman.code AS code_jasa_pengiriman
            FROM
                letter
            INNER JOIN
                jasa_pengiriman ON jasa_pengiriman.id = letter.jasa_pengiriman_id
            WHERE
                letter.jasa_pengiriman_id != 1
                AND letter.status = '" . self::LETTER_STATUS_IN_DELIVERY . "'
                AND letter.receipt_no IS NOT NULL
                AND jasa_pengiriman.code IS NOT NULL
                AND ROWNUM <= " . self::BATCH_SIZE;

        return QueryAPI::get($query) ?? [];
    }

    /**
     * updateLetterStatus
     *
     * @param  mixed $letter
     * @return void
     */
    private function updateLetterStatus($letter)
    {
        try {
            if (empty($letter->RECEIPT_NO) || empty($letter->CODE_JASA_PENGIRIMAN)) {
                Log::warning('Invalid letter data', [
                    'letter_id' => $letter->LETTER_ID ?? 'unknown',
                    'receipt_no' => $letter->RECEIPT_NO ?? null,
                    'courier_code' => $letter->CODE_JASA_PENGIRIMAN ?? null
                ]);

                return false;
            }

            $trackingData = $this->fetchTrackingData(
                $letter->RECEIPT_NO,
                $letter->CODE_JASA_PENGIRIMAN
            );

            if (!$trackingData) {
                Log::warning('No tracking data received', [
                    'letter_id' => $letter->LETTER_ID,
                    'receipt_no' => $letter->RECEIPT_NO
                ]);

                return false;
            }

            if ($this->isDelivered($trackingData)) {
                $this->markAsDelivered($letter, $trackingData);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to update letter', [
                'letter_id' => $letter->LETTER_ID ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * fetchTrackingData
     *
     * @param  mixed $receiptNo
     * @param  mixed $courierCode
     * @return void
     */
    private function fetchTrackingData($receiptNo, $courierCode)
    {
        try {
            $buildQuery = http_build_query([
                'awb' => $receiptNo,
                'courier' => $courierCode
            ]);

            $response = RajaOngkir::post('track/waybill?' . $buildQuery);

            if (!$response || !isset($response->data)) {
                return null;
            }

            return $response->data;
        } catch (\Exception $e) {
            Log::error('RajaOngkir API failed', [
                'receipt_no' => $receiptNo,
                'courier' => $courierCode,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * isDelivered
     *
     * @param  mixed $trackingData
     * @return void
     */
    private function isDelivered($trackingData)
    {
        if (!$trackingData) {
            return false;
        }

        return isset($trackingData->delivery_status->status) && $trackingData->delivery_status->status === self::DELIVERY_STATUS_DELIVERED;
    }

    /**
     * markAsDelivered
     *
     * @param  mixed $letter
     * @param  mixed $trackingData
     * @return void
     */
    private function markAsDelivered($letter, $trackingData)
    {
        try {
            $manifest = $trackingData->manifest[0] ?? null;

            if (!$manifest) {
                Log::warning('No manifest data available', [
                    'letter_id' => $letter->LETTER_ID
                ]);

                return;
            }

            $sentDate = $this->formatSentDate(
                $manifest->manifest_date ?? null,
                $manifest->manifest_time ?? null
            );

            if (!$sentDate) {
                Log::warning('Invalid sent date', [
                    'letter_id' => $letter->LETTER_ID,
                    'manifest_date' => $manifest->manifest_date ?? null,
                    'manifest_time' => $manifest->manifest_time ?? null
                ]);

                return;
            }

            $updateData = [
                'sent_date' => $sentDate,
                'status' => self::LETTER_STATUS_DELIVERED
            ];

            $result = QueryAPI::update('letter', $letter->LETTER_ID, $updateData, false);

            if ($result) {
                Log::info('Letter marked as delivered', [
                    'letter_id' => $letter->LETTER_ID,
                    'sent_date' => $sentDate,
                    'receipt_no' => $letter->RECEIPT_NO
                ]);
            } else {
                Log::error('Failed to update letter status in database', [
                    'letter_id' => $letter->LETTER_ID
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error marking letter as delivered', [
                'letter_id' => $letter->LETTER_ID,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * formatSentDate
     *
     * @param  mixed $date
     * @param  mixed $time
     * @return void
     */
    private function formatSentDate($date, $time)
    {
        if (!$date) {
            return null;
        }

        try {
            $dateTimeString = trim($date . ' ' . ($time ?? '00:00:00'));
            $dateTime = Carbon::parse($dateTimeString);

            return $dateTime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::warning('Failed to parse date', [
                'date' => $date,
                'time' => $time,
                'error' => $e->getMessage()
            ]);

            return $date . ' ' . ($time ?? '00:00:00');
        }
    }
}
