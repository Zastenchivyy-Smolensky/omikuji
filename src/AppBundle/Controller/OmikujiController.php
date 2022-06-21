<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;   // (a)
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // (b)
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Unsei; 
use AppBundle\Repository\UnseiRepository;

class OmikujiController extends Controller
{
    /**
     * @Route("/omikuji/{yourname}", defaults={"yourname" = "YOU"}, name="omikuji")
     * @param Request $request
     * @return Request
     */
    public function omikujiAction(Request $request, $yourname)
    {
        $repository = $this->getDoctrine()->getRepository(Unsei::class); // ①
        $omikuji = $repository->findAll();
        $number = rand(0, count($omikuji)-1);
        return $this->render("omikuji/omikuji.html.twig",[
            "name" => $yourname,
            "unsei" => $omikuji[$number]
        ]);
    }
    /**
     * @Route("/crud")
     */

    public function crudAction()
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        //Create
        $unsei = new Unsei();
        $unsei->setName("大凶");
        dump($unsei);
        $em->persist($unsei);
        $em->flush();
        dump($unsei);

        //Read
        $repository = $em->getRepository(Unsei::class);
        /** @var Unsei $unsei */
        $unsei = $repository->findOneByName("大凶");
        dump($unsei);
        //update
        $unsei->setName("大大凶");
        $em->flush();
        dump($unsei);

        $unsei=$repository->find($unsei->getId());
        dump($unsei);

        //delete
        $em->remove($unsei);
        $em->flush();

        $unseis = $repository->findAll();
        dump($unseis);
        foreach ($unseis as $unsei) {
            dump($unsei->getName());
        }
        
        die; 

        return new Response("Dummy");
    }
    /**
     * @Route("/dql")
     */
    public function dql()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT u
            FROM AppBundle:Unsei u
            WHERE u.name = :name'        // ①
        )->setParameter('name', '大吉');
        $unsei = $query->getResult();
        dump($unsei);
        die;
        return new Response("Dummy");
    }
    /**
     * @Route("/qb")
     */
    public function queryBuilder()
    {
        /** @var UnseiRepository $repository **/
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        
        $query = $repository->createQueryBuilder('u')
            ->where('u.name = :name')
            ->setParameter('name', '大吉')
            ->getQuery();

        $unsei = $query->getResult();
        dump($unsei);
        
        die; // プログラムを終了して、dumpを画面に表示

        return new Response("Dummy");
    }

    /**
     * @Route("/find")
     */
    public function findAction()
    {
        /**
         * @var UnseiRepository $repository
         */
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $unseis = $repository->findAll();
        dump($unseis);
        $unsei = $repository->find(1);
        dump($unsei);
        $unsei = $repository->findOneBy([
            'name' => '大吉',
        ]);
        dump($unsei);
        
        // 複数の項目で複数件検索（ここでは'name'だけですが...）⑤
        // ※ 配列が返ってくる
        $unsei = $repository->findBy([
            'name' => '大吉',
        ]);
        dump($unsei);
        
        // プロパティに対応したダイナミックメソッドを使って１件だけ検索 ⑥
        $unsei = $repository->findOneById(1);
        dump($unsei);
        $unsei = $repository->findOneByName('中吉');
        dump($unsei);
        
        // ダイナミックメソッドを使って複数件検索 ⑦
        $unsei = $repository->findByName('中吉');
        dump($unsei);
        
        die; // プログラムを終了して、dumpを画面に表示 ⑧
        return new Response("Dummy");
    }
}