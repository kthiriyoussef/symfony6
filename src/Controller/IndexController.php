<?php
namespace App\Controller;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PropertySearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;
use App\Entity\Category;
use App\Entity\PropertySearch;
use App\Form\CategoryType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;
use Doctrine\Persistence\ManagerRegistry;
class IndexController extends AbstractController
{
    #[Route('/', name: 'home')]
 public function home(ManagerRegistry $doctrine,Request $request): Response
 {
   $propertySearch = new PropertySearch();
   $form = $this->createForm(PropertySearchType::class,$propertySearch);
   $form->handleRequest($request);
   //initialement le tableau des articles est vide,
   //c.a.d on affiche les articles que lorsque l'utilisateur
   //clique sur le bouton rechercher
   $articles= [];
   
   if($form->isSubmitted() && $form->isValid()) {
   //on récupère le nom d'article tapé dans le formulaire
   $nom = $propertySearch->getNom();
 if ($nom!="")
 {
   $entityManager = $doctrine->getManager();
   $articles= $entityManager->getRepository(Article::class)->findBy(['Nom' => $nom] );
 }
 //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
 
 else
 {
   $entityManager = $doctrine->getManager();
   $articles= $entityManager->getRepository(Article::class)->findAll();
 }
 //si si aucun nom n'est fourni on affiche tous les articles
 
 }
 return $this->render('articles/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
 }
 

 #[Route('/aricle/new', name: 'new_article',methods: ['GET', 'POST'])]
 public function new(Request $request,ManagerRegistry $doctrine) {
   $article = new Article();
   $form = $this->createForm(ArticleType::class,$article);
   $form->handleRequest($request);
   if($form->isSubmitted() && $form->isValid()) {
   $article = $form->getData();
   $entityManager = $doctrine->getManager();
   $entityManager->persist($article);
   $entityManager->flush();
   return $this->redirectToRoute('home');
   }
   return $this->render('articles/new.html.twig',['form' => $form->createView()]);
   }
  
    #[Route('/article/{id}', name: 'show_article')]
    public function show($id,ManagerRegistry $doctrine) {
      $article = $doctrine->getRepository(Article::class)->find($id);
      return $this->render('articles/show.html.twig',array('article' => $article));
       }
       #[Route('/article/edit/{id}', name: 'edit_article',methods: ['GET', 'POST'])]
 public function edit(Request $request, $id,ManagerRegistry $doctrine) {
   $article = new Article();
   $article = $doctrine->getRepository(Article::class)->find($id);
   
    $form = $this->createForm(ArticleType::class,$article);
   
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
   
    $entityManager = $doctrine->getManager();
    $entityManager->flush();
   
    return $this->redirectToRoute('home');
    }
   
    return $this->render('articles/edit.html.twig', ['form' =>$form->createView()]);
 }

 #[Route('/article/delete/{id}',name:'delete_article')]
 public function delete(Request $request, $id,ManagerRegistry $doctrine) {
   $article = $doctrine->getRepository(Article::class)->find($id);
  
   $entityManager = $doctrine->getManager();
   $entityManager->remove($article);
   $entityManager->flush();
  
   $response = new Response();
   $response->send();
   return $this->redirectToRoute('home');
   }

   #[Route('/category/newCat',name:'new_Category',methods :['GET','POST'])]
   public function newCategory(Request $request,ManagerRegistry $doctrine) {
      $category = new Category();
      
      $form = $this->createForm(CategoryType::class,$category);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()) {
      $article = $form->getData();
      $entityManager = $doctrine->getManager();
      $entityManager->persist($category);
      $entityManager->flush();
      }
     return $this->render('categories/newCategory.html.twig',['form'=>$form->createView()]);
      }


/**
 * @Route("/art_cat/", name="article_par_cat")
 * Method({"GET", "POST"})
 */
#[Route('/art_cat/',name:'article_par_cat',methods :['GET','POST'])]
public function articlesParCategorie(Request $request,ManagerRegistry $doctrine) {
   $categorySearch = new CategorySearch();
   $form = $this->createForm(CategorySearchType::class,$categorySearch);
   $form->handleRequest($request);
   $articles= [];
   if($form->isSubmitted() && $form->isValid()) {
      $category = $categorySearch->getCategory();
     
      if ($category!="")
     $articles= $category->getArticles();
      else
      $articles= $this->$doctrine->getRepository(Article::class)->findAll();
      }
     
      return $this->render('articles/articlesParCategorie.html.twig',['form' => $form->createView(),'articles' => $articles]);
      }
   

      #[Route('/art_prix/"',name:'article_par_prix',methods :['GET','POST'])]
      public function articlesParPrix(Request $request,ManagerRegistry $doctrine)
      {
     
      $priceSearch = new PriceSearch();
      $form = $this->createForm(PriceSearchType::class,$priceSearch);
      $form->handleRequest($request);
      $articles= [];
      if($form->isSubmitted() && $form->isValid()) {
      $minPrice = $priceSearch->getMinPrice();
      $maxPrice = $priceSearch->getMaxPrice();
      $entityManager = $doctrine->getManager();
      $articles= $entityManager->getRepository(Article::class)->findByPriceRange($minPrice, $maxPrice);
      
      }
      return $this->render('articles/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
      }
   

   
}
?>