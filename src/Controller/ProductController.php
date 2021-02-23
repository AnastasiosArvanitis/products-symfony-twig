<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products_list", name="products_list")
     */
    public function products(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/products.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product_detail/{id}", name="product_detail", methods={"GET"})
     * @param $id
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function product($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        return $this->render('product/product.html.twig', [
           'product' => $product,
        ]);
    }

    /**
     * @Route("/new_product", name="new_product", methods={"GET","POST"})
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function create_product(EntityManagerInterface $entityManager, Request $request): Response {
        $product = new Product();
        $product->setDateCreate(new \DateTime());

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'The product was added!');
            return $this->redirectToRoute('product_detail', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('product/add.html.twig', [
            "productForm" => $productForm->createView()
        ]);
    }

    /**
     * @Route("/edit_product/{id}", name="edit_product", methods={"GET", "PUT", "POST"})
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit_product(EntityManagerInterface $entityManager, Request $request, $id): Response {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
            dump($product);

            $this->addFlash('success', 'The product was edited!');
            return $this->redirectToRoute('product_detail', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('product/edit.html.twig', [
            "productForm" => $productForm->createView()
        ]);
    }

    /**
     * * @Route("/delete_product/{id}", name="delete_product", methods={"GET", "PUT"})
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return Response
     */
    public function delete_product(EntityManagerInterface $entityManager, $id): Response {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        } else {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('products_list');
    }
}
















