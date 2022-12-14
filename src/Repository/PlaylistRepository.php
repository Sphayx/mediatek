<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity, bool $flush = false): void {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Playlist $entity, bool $flush = false): void {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne toutes les playlists triées sur un champ
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderBy($champ, $ordre): array {
        return $this->createQueryBuilder('p')
                        ->select('p.id id')
                        ->addSelect('p.name name')
                        ->addSelect('c.name categoriename')
                        ->addSelect('count(f.playlist) nbformations')
                        ->leftjoin('p.formations', 'f')
                        ->leftjoin('f.categories', 'c')
                        ->groupBy('p.id')
                        ->addGroupBy('c.name')
                        ->orderBy($champ, $ordre)
                        ->addOrderBy('c.name')
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Enregistrements dont un champ dans une autre table contient une valeur 
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table 
     * @return Playlist[]
     */
    public function findByContainValueTable($champ, $valeur, $table): array {
        if ($valeur == "") {
            return $this->findAllOrderBy('name', 'ASC');
        }
        return $this->createQueryBuilder('p')
                        ->select('p.id id')
                        ->addSelect('p.name name')
                        ->addSelect('c.name categoriename')
                        ->addSelect('count(f.playlist) nbformations')
                        ->leftjoin('p.formations', 'f')
                        ->leftjoin('f.categories', 'c')
                        ->where('c.' . $champ . ' LIKE :valeur')
                        ->setParameter('valeur', '%' . $valeur . '%')
                        ->groupBy('p.id')
                        ->addGroupBy('c.name')
                        ->orderBy('p.name', 'ASC')
                        ->addOrderBy('c.name')
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur): array {
        if ($valeur == "") {
            return $this->findAllOrderBy('name', 'ASC');
        }

        return $this->createQueryBuilder('p')
                        ->select('p.id id')
                        ->addSelect('p.name name')
                        ->addSelect('c.name categoriename')
                        ->addSelect('count(f.playlist) nbformations')
                        ->leftjoin('p.formations', 'f')
                        ->leftjoin('f.categories', 'c')
                        ->where('p.' . $champ . ' LIKE :valeur')
                        ->setParameter('valeur', '%' . $valeur . '%')
                        ->groupBy('p.id')
                        ->addGroupBy('c.name')
                        ->orderBy('p.name', 'ASC')
                        ->addOrderBy('c.name')
                        ->getQuery()
                        ->getResult();
    }
        
}

//     Non fonctionnel
//     /**
//     * 
//     * @param type $idPlaylist
//     * @return Playlist|null
//     */
//    public function findOneByPlaylist($idPlaylist): Playlist {
//        return $this->createQueryBuilder('p')
//                        ->select('p.id id')
//                        ->addSelect('p.name name')
//                        ->addSelect('p.description description')
//                        ->addSelect('count(f.playlist) nbformations')
//                        ->join('p.formations', 'f')
//                        ->where('p.id=:id')
//                        ->setParameter('id', $idPlaylist)
//                        ->orderBy('p.id')
//                        ->getQuery()
//                        ->getSingleResult();
//    }

