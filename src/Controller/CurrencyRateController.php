<?php

namespace App\Controller;
use App\Service\CurrencyRateService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
class CurrencyRateController
{
    private CurrencyRateService $service;

    public function __construct(CurrencyRateService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/api/rates/historical", name="get_historical_rates", methods={"GET"})
     */
    public function getHistoricalRates(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $constraints = new Assert\Collection([
            'pair' => new Assert\NotBlank(),
            'start_date' => new Assert\DateTime(),
            'end_date' => new Assert\DateTime(),
        ]);

        $input = [
            'pair' => $request->query->get('pair'),
            'start_date' => $request->query->get('start_date'),
            'end_date' => $request->query->get('end_date'),
        ];

        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }
            return $this->json(['errors' => $errors], 400);
        }

        $pair = $input['pair'];
        $startDate = new \DateTimeImmutable($input['start_date']);
        $endDate = new \DateTimeImmutable($input['end_date']);

        $rates = $this->service->getHistoricalRates($pair, $startDate, $endDate);

        return $this->json($rates);  // Symfony автоматически сериализует DTO в массив
    }

    /**
     * @Route("/api/rates/update", name="update_rates", methods={"POST"})
     */
    public function updateRates(): JsonResponse
    {
        $this->service->updateRates();

        return $this->json(['status' => 'Rates updated successfully']);
    }
}