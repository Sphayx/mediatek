<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminCategorieController extends AbstractController {

    private $categorieRepository;

    /**
     * 
     * @param CategorieRepository $repository
     */
    public function __construct(CategorieRepository $repository) {
        $this->categorieRepository = $repository;
    }

    public function add(Categorie $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @Route("/admin/categories", name="admin.categories")
     * @return Response
     */
    public function index(): Response {
        $categories = $this->categorieRepository->findAllOrderBy('name', 'ASC');
        return $this->render("admin/admin.categories.html.twig", [
                    'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/categorie/ajout", name="admin.categorie.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response {
        $nomCategorie = $request->get("nom");
        $nomTest = $this->categorieRepository->findAllNameEqual($nomCategorie);
        if ($nomTest == false) {
            $categorie = new Categorie();
            $categorie->setName($nomCategorie);
            $this->categorieRepository->add($categorie, true);
            return $this->redirectToRoute('admin.categories');
        }
        else{
            
            return $this->redirectToRoute('admin.categories');
        }
    }

    /**
     * @Route("admin/categorie/suppr/{id}", name="admin.categorie.suppr")
     * @param Categorie $categorie
     * @return Response
     */
    public function suppr(Categorie $categorie): Response {
        $this->categorieRepository->remove($categorie, true);
        return $this->redirectToRoute('admin.categories');
    }
    
    /**
     * Retourne la liste des catégories des formations d'une playlist
     * @param type $idPlaylist
     * @return Categorie[]
     */
    public function findAllForOnePlaylist($idPlaylist): array {
        return $this->createQueryBuilder('c')
                        ->join('c.formations', 'f')
                        ->join('f.playlist', 'p')
                        ->where('p.id=:id')
                        ->setParameter('id', $idPlaylist)
                        ->orderBy('c.name', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

}
