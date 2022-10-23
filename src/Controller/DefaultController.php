<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductFormType;
use App\Repository\ProductRepository;
use Monolog\DateTimeImmutable;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function Symfony\Bundle\FrameworkBundle\Controller\redirectToRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;


class DefaultController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct( EntityManagerInterface $entityManager,
                                 ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    #[Route('/', name: 'main_homepage')]
    public function index(): Response
    {
        $this->entityManager->getEventManager();
        $this->entityManager->getRepository(Product::class);
        $productList = $this->productRepository->findAll();
        return $this->render('main/default/index.html.twig');
    }

//    #[Route('/product-add', name: 'product_add')]
//    public function productAdd(): Response
//    {
//        $product = new Product();
//        $product->setTitle('Product '.rand(1, 100));
//        $product->setDescription("sfdsfsf");
//        $product->setPrice(10);
//        $product->setQuantity(1);
//        $product->setCreatedAt(new DateTimeImmutable(\DateTimeZone::EUROPE));
//
//        $this->entityManager->getEventManager();
//        $this->entityManager->persist($product);
//        $this->entityManager->flush();
//
//        return $this->redirectToRoute('homepage');
//    }

    #[Route('/edit-product/{id}', name: 'product_edit')]
    #[Route('/add-product', name: 'product_add')]
    public function editProduct(Request $request, int $id=null): Response
    {
        $this->entityManager->getEventManager();

        if($id){
            $product = $productList = $this->productRepository->find($id);
        } else {
            $product = new Product();
            $product->setCreatedAt(new DateTimeImmutable(\DateTimeZone::EUROPE));
        }
        $form = $this->createForm(EditProductFormType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->renderForm('main/default/edit_product.html.twig', [
            'form' => $form,
        ]);
    }
}
