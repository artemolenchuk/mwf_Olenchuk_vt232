<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Rental;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payments')]
class PaymentController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $repository = $em->getRepository(Payment::class);
        $criteria = [];

        if ($request->query->has('id')) {
            $criteria['id'] = $request->query->get('id');
        }
        if ($request->query->has('rental_id')) {
            $criteria['rental'] = $request->query->get('rental_id');
        }
        if ($request->query->has('amount')) {
            $criteria['amount'] = $request->query->get('amount');
        }
        if ($request->query->has('payment_method')) {
            $criteria['paymentMethod'] = $request->query->get('payment_method');
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = max(1, (int) $request->query->get('itemsPerPage', $request->query->get('per_page', 10)));
        $offset = ($page - 1) * $itemsPerPage;

        $totalItems = $repository->count($criteria);
        $payments = $repository->findBy($criteria, null, $itemsPerPage, $offset);

        $data = array_map(fn($p) => [
            'id' => $p->getId(),
            'rental_id' => $p->getRental()?->getId(),
            'amount' => $p->getAmount(),
            'payment_date' => $p->getPaymentDate()?->format('Y-m-d H:i:s'),
            'payment_method' => $p->getPaymentMethod(),
        ], $payments);

        return $this->json([
            'data' => $data,
            'total' => $totalItems,
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'last_page' => (int) ceil($totalItems / $itemsPerPage)
        ], Response::HTTP_OK);
    }

    #[Route('', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $rental = $em->getRepository(Rental::class)->find($data['rental_id'] ?? 0);

        if (!$rental) return $this->json(['error' => 'Rental not found'], Response::HTTP_BAD_REQUEST);

        $payment = new Payment();
        $payment->setRental($rental);
        $payment->setAmount($data['amount'] ?? '100.00');
        $payment->setPaymentDate(new \DateTime($data['payment_date'] ?? 'now'));
        $payment->setPaymentMethod($data['payment_method'] ?? 'card');

        $em->persist($payment);
        $em->flush();
        return $this->json(['id' => $payment->getId(), 'amount' => $payment->getAmount()], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function destroy(int $id, EntityManagerInterface $em): JsonResponse
    {
        $payment = $em->getRepository(Payment::class)->find($id);
        if (!$payment) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $em->remove($payment);
        $em->flush();
        return $this->json(['message' => 'Deleted'], Response::HTTP_OK);
    }
}