<?php

namespace App\Service;

use App\Dto\CurrencyRateDto;
use App\Repository\CurrencyRateRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CurrencyRateService
{
    private CurrencyRateRepository $repository;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private CacheInterface $cache;

    public function __construct(CurrencyRateRepository $repository, HttpClientInterface $httpClient, LoggerInterface $logger, CacheInterface $cache)
    {
        $this->repository = $repository;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * Fetches current rates from external API and updates the database.
     */
    public function updateRates(): void
    {
        try {
            $apiResponse = $this->fetchRatesFromApi();

            foreach ($apiResponse as $pair => $rate) {
                $this->repository->saveRate($pair, $rate);
            }

            $this->logger->info('Currency rates updated successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to update currency rates: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch historical rates for a specific currency pair within a date range.
     *
     * @param string $pair
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return CurrencyRateDto[]
     */
    public function getHistoricalRates(string $pair, \DateTime $startDate, \DateTime $endDate): array
    {
        $cacheKey = sprintf('historical_rates_%s_%s_%s', $pair, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        return $this->cache->get($cacheKey, function () use ($pair, $startDate, $endDate) {
            $rates = $this->repository->getRatesByDateRange($pair, $startDate, $endDate);
            return array_map(fn($rate) => new CurrencyRateDto(
                $rate->getPair(),
                $rate->getRate(),
                $rate->getUpdatedAt()
            ), $rates);
        });
    }

    /**
     * Fetch rates from external API.
     *
     * @return array
     */
    private function fetchRatesFromApi(): array
    {
        try {
            $response = $this->httpClient->get('https://api.coingecko.com/api/v3/simple/price', [
                'query' => [
                    'ids' => 'bitcoin',
                    'vs_currencies' => 'usd,eur,gbp',
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to fetch rates: Unexpected status code ' . $response->getStatusCode());
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['bitcoin'])) {
                throw new \Exception('Invalid response: Missing "bitcoin" key');
            }

            return [
                'BTC/USD' => $data['bitcoin']['usd'],
                'BTC/EUR' => $data['bitcoin']['eur'],
                'BTC/GBP' => $data['bitcoin']['gbp'],
            ];
        } catch (\Exception $e) {
            $this->logger->error('Error fetching rates: ' . $e->getMessage());
            throw new \Exception('Error fetching rates: ' . $e->getMessage(), 0, $e);
        }
    }
}