<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author emds
 */
class AdminFormationController extends AbstractController {

    /**
     *
     * @var PagesFormations
     */
    private $pagesFormations = "admin/admin.formations.html.twig";

    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;

    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;

    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render($this->pagesFormations, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/tri/{champ}/{ordre}/{table}", name="admin.formations.sorttable")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sortTable($champ, $ordre, $table): Response {
        $formations = $this->formationRepository->findAllOrderByTable($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render($this->pagesFormations, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/tri/{champ}/{ordre}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre);
        $categories = $this->categorieRepository->findAll();
        return $this->render($this->pagesFormations, [
                    'formations' => $formations,
                    'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/recherche/{champ}/{table}", name="admin.formations.findallcontaintable")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainTable($champ, Request $request, $table): Response {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValueTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render($this->pagesFormations, [
                    'formations' => $formations,
                    'categories' => $categories,
                    'valeur' => $valeur,
                    'table' => $table
        ]);
    }

    /**
     * @Route("/admin/recherche/{champ}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render($this->pagesFormations, [
                    'formations' => $formations,
                    'categories' => $categories,
                    'valeur' => $valeur,
        ]);
    }

    /**
     * @Route("/admin/formation/{id}", name="admin.formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
                    'formation' => $formation
        ]);
    }

    /**
     * @Route("/admin/suppr/{id}", name="admin.formation.suppr")
     * @param Formation $formation
     * @return Response
     */
    public function suppr(Formation $formation): Response {
        $this->formationRepository->remove($formation, true);
        return $this->redirectToRoute('admin.formations');
    }

    /**
     * @Route("/admin/ajout", name="admin.formation.ajout")
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response {
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);

        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render("admin/admin.formation.ajout.html.twig", [
                    'formation' => $formation,
                    'formformation' => $formFormation->createView()
        ]);
    }

    /**
     * @Route("/admin/edit/{id}", name="admin.formation.edit")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formation, Request $request): Response{
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/admin.formation.edit.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);        
    }

}
