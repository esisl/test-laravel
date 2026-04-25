<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiFetcherService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected int $retryCount;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('api.base_url'), '/');
        $this->apiKey = config('api.key');
        $this->timeout = config('api.timeout', 30);
        $this->retryCount = config('api.retry', 3);
    }

    public function fetchEntity(string $entity, string $dateFrom, string $dateTo): void
    {
        $path = config("api.entities.{$entity}");
        if (!$path) {
            throw new \InvalidArgumentException("Entity '{$entity}' not configured.");
        }

        Log::info("Starting fetch for entity: {$entity}", compact('dateFrom', 'dateTo'));

        $page = 1;
        $lastPage = null;
        $totalInserted = 0;

        do {
            try {
                $response = Http::timeout($this->timeout)
                    ->retry($this->retryCount, 500)
                    ->get("{$this->baseUrl}{$path}", [
                        'dateFrom' => $dateFrom,
                        'dateTo' => $dateTo,
                        'page' => $page,
                        'key' => $this->apiKey,
                    ]);

                if (!$response->successful()) {
                    $msg = "API failed on page {$page}. Status: {$response->status()}. Body: {$response->body()}";
                    Log::error($msg);
                    if ($page === 1) {
                        throw new \RuntimeException("Critical API error: {$msg}");
                    }
                    break;
                }

                $data = $response->json();
                if (empty($data['data']) || !is_array($data['data'])) {
                    Log::warning("Empty/malformed data on page {$page}.");
                    if ($page === 1) break;
                    continue;
                }

                if ($lastPage === null) {
                    $lastPage = $data['meta']['last_page'] ?? 1;
                    Log::info("Total pages detected: {$lastPage}");
                }

                $records = $this->mapToDbFormat($data['data'], $entity);
                
                // Пакетная вставка без проверок уникальности (сырой сбор)
                DB::table("{$entity}")->insert($records);
                $totalInserted += count($records);

                Log::info("Inserted page {$page}/{$lastPage}. Records: " . count($records));

            } catch (\Exception $e) {
                Log::error("Exception on page {$page}: " . $e->getMessage());
                if ($page === 1) throw $e;
                break;
            }

            $page++;
        } while ($page <= $lastPage);

        if ($totalInserted === 0) {
            Log::warning("Fetch completed, but 0 records inserted. Check storage/logs/laravel.log for details.");
        } else {
            Log::info("Fetch completed for {$entity}. Total inserted: {$totalInserted}");
        }
    }

    protected function mapToDbFormat(array $data, string $entity): array
    {
        $mapped = [];
        foreach ($data as $item) {
            $record = [
                'g_number' => $item['g_number'] ?? null,
                'date' => $item['date'] ?? now(),
                'last_change_date' => $item['last_change_date'] ?? now(),
                'supplier_article' => $item['supplier_article'] ?? null,
                'tech_size' => $item['tech_size'] ?? null,
                'barcode' => $item['barcode'] ?? null,
                'nm_id' => $item['nm_id'] ?? null,
                'total_price' => $item['total_price'] ?? 0,
                'discount_percent' => $item['discount_percent'] ?? 0,
                'warehouse_name' => $item['warehouse_name'] ?? '',
                'income_id' => $item['income_id'] ?? 0,
                'subject' => $item['subject'] ?? null,
                'category' => $item['category'] ?? null,
                'brand' => $item['brand'] ?? null,
                'raw_data' => json_encode($item, JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Entity-specific fields
            if ($entity === 'sales') {
                $record['sale_id'] = $item['sale_id'] ?? null;
                $record['for_pay'] = $item['for_pay'] ?? 0;
                $record['finished_price'] = $item['finished_price'] ?? 0;
                $record['price_with_disc'] = $item['price_with_disc'] ?? 0;
                $record['spp'] = $item['spp'] ?? null;
                $record['is_supply'] = (bool)($item['is_supply'] ?? false);
                $record['is_realization'] = (bool)($item['is_realization'] ?? false);
                $record['is_storno'] = isset($item['is_storno']) ? (bool)$item['is_storno'] : null;
                $record['country_name'] = $item['country_name'] ?? '';
                $record['oblast_okrug_name'] = $item['oblast_okrug_name'] ?? '';
                $record['region_name'] = $item['region_name'] ?? '';
                $record['promo_code_discount'] = $item['promo_code_discount'] ?? null;
            }

            if ($entity === 'orders') {
                $record['odid'] = $item['odid'] ?? null;
                $record['oblast'] = $item['oblast'] ?? '';
                $record['is_cancel'] = (bool)($item['is_cancel'] ?? false);
                $record['cancel_dt'] = $item['cancel_dt'] ?? null;
                // Для orders date приходит как datetime, приводим к формату БД
                $record['date'] = is_string($record['date']) ? $record['date'] : now()->format('Y-m-d H:i:s');
            }

            // stocks и incomes добавим позже по аналогии

            $mapped[] = $record;
        }
        return $mapped;
    }
}