<?php
/**
 * © Julkwel <julienrajerison5@gmail.com>
 *
 * Fokontany Controller.
 */

namespace App\Controller\Admin;

use App\Constant\MessageConstant;
use App\Constant\PageConstant;
use App\Controller\AbstractBaseController;
use App\Entity\Employee;
use App\Entity\Fokontany;
use App\Entity\User;
use App\Form\FokontanyType;
use App\Repository\FokontanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nzo\UrlEncryptorBundle\UrlEncryptor\UrlEncryptor;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class FokontanyController.
 *
 * @Route("/fokontany")
 */
class FokontanyController extends AbstractBaseController
{
    /** @var FokontanyRepository */
    private $repository;

    /**
     * FokontanyController constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param UrlEncryptor                 $urlEncrypt
     * @param PaginatorInterface           $paginator
     * @param FokontanyRepository          $fokontanyRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, UrlEncryptor $urlEncrypt, PaginatorInterface $paginator, FokontanyRepository $fokontanyRepository)
    {
        parent::__construct($entityManager, $userPasswordEncoder, $urlEncrypt, $paginator);
        $this->repository = $fokontanyRepository;
    }

    /**
     * @Route("/manage/{id?}", name="fokontany_manage", methods={"POST","GET"})
     *
     * @param Request     $request
     * @param string|null $id
     *
     * @return RedirectResponse|Response
     */
    public function manageFokontany(Request $request, ?string $id = null)
    {
        $fokontany = $this->repository->find($this->decryptThisId($id)) ?? new Fokontany();
        $form = $this->createForm(FokontanyType::class, $fokontany);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->save($fokontany)) {
                $this->addFlash(MessageConstant::SUCCESS_TYPE, 'Tafiditra ny fokontany nampidirinao');

                return $this->redirectToRoute('fokontany_list');
            }
            $this->addFlash(MessageConstant::ERROR_TYPE, 'Misy olana ny fokontaniko');

            return $this->redirectToRoute('fokontany_manage', ['id' => $this->encryptThisId($fokontany->getId())]);
        }

        return $this->render('admin/fokontany/_fokontany_form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/list", name="fokontany_list", methods={"POST","GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function fokontanyList(Request $request)
    {
        $pagination = $this->paginator->paginate(
            $this->repository->findAllFokontany(),
            $request->query->getInt('page', PageConstant::DEFAULT_PAGE),
            PageConstant::DEFAULT_NUMBER_PER_PAGE
        );

        return $this->render('admin/fokontany/_fokontany_list.html.twig', ['fokontanys' => $pagination]);
    }

    /**
     * @Route("/responsable/{id}", name="fokontany_responsable", methods={"POST","GET"})
     *
     * @param Request     $request
     * @param string|null $id
     *
     * @return Response
     */
    public function addResponsable(Request $request, ?string $id)
    {
        $fokontany = $this->repository->find($this->decryptThisId($id));
        if ('POST' === $request->getMethod()) {

            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['userName' => $request->get('username')]);

            /** @var Employee $responsable */
            $responsable = $this->entityManager->getRepository(Employee::class)->findOneBy(['user' => $user]);
            if ($fokontany) {
                $fokontany->addResponsable($responsable);

                if ($this->save($fokontany)) {
                    $this->addFlash(MessageConstant::SUCCESS_TYPE, 'Voaray ny fanovana');

                    return $this->redirectToRoute('fokontany_list');
                }
                $this->addFlash(MessageConstant::ERROR_TYPE, 'Misy olana ny fokontaniko');

                return $this->redirectToRoute('fokontany_responsable', ['fokontany' => $id]);
            }

        }

        return $this->render('admin/fokontany/_fokontany_responsable.html.twig', ['fokontany' => $fokontany]);
    }
}
