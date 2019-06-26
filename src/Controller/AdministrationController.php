<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\FriendRequest;
use App\Entity\Statistics;
use App\Entity\Laws;
use App\Entity\Report;
use App\Entity\Survey;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Yaml\Yaml;

class AdministrationController extends AbstractController
{
    /**
     * @Route("/{_locale}/admin", name="administration", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function administrationAction()
    {
        $this->denyAccessUnlessGranted('administration.access');
        return $this->render('administration/index.html.twig');
    }

    /**
     * @Route("/{_locale}/admin/complaints_office", name="administration_complaints_office", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function complaintsOfficeAction()
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.complaints_office');
        return $this->render('administration/complaints_office.html.twig');
    }

    /**
     * @Route("/{_locale}/admin/manage_roles", name="administration_manage_roles", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function manageRolesAction()
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.manage_roles');
        return $this->render('administration/manage_roles.html.twig');
    }

    /**
     * @Route("/{_locale}/admin/edit_website", name="administration_edit_website", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function editWebsiteAction(Request $request)
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.edit_website');
        $yaml = Yaml::parseFile(__dir__.'/../../config/packages/twig.yaml');
        $form = $this->container->get('form.factory')->createNamedBuilder('edit_website', FormType::class, ['version' => $yaml['twig']['globals']['is_version_displayed']], ['action' => $this->generateUrl('administration_edit_website')])
            ->add('HAYlogo', FileType::class, array('required' => false))
            ->add('icon', FileType::class, array('required' => false))
            ->add('version', CheckboxType::class, array('required' => false))
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();

            if ($datas['HAYlogo'] !== NULL) {
                if ($datas['HAYlogo']->guessExtension() == 'svg' || $datas['HAYlogo']->guessExtension() == 'svgz') {
                    $datas['HAYlogo']->move(__dir__.'/../../public/ressources/', 'HAYlogo.svg');
                } else {
                    throw new \Exception('The image has to be of type SVG.');
                }
            }

            if ($datas['icon'] !== NULL) {
                if ($datas['icon']->guessExtension() == 'svg' || $datas['icon']->guessExtension() == 'svgz') {
                    $datas['icon']->move(__dir__.'/../../public/ressources/', 'icon.svg');
                } else {
                    throw new \Exception('The image has to be of type SVG.');
                }
            }

            $yaml['twig']['globals']['is_version_displayed'] = $datas['version'];
            file_put_contents(__dir__.'/../../config/packages/twig.yaml', Yaml::dump($yaml));
            return $this->redirectToRoute('administration');
        }

        return $this->render('administration/edit_website.html.twig', [
            'edit_website' => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/admin/statistics", name="administration_statistics", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function statisticsAction()
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.statistics');
        return $this->render('administration/statistics.html.twig');
    }

    /**
     * @Route("/{_locale}/admin/sql_interface", name="administration_sql_interface", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function sqlInterfaceAction()
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.sql_interface');
        // If you found another file that should be excluded, please open an issue
        $entities = array_diff(
            scandir(__dir__.'/../Entity'),
            array(
                '..',
                '.',
                '.gitignore',
                '.DS_Store',
                '.DS_Store?',
                '.Spotlight-V100',
                '.Trashes',
                'ehthumbs.db',
                'Thumbs.db'
            )
        );

        foreach ($entities as $entitykey => $entity) {
            $entities[$entitykey] = substr($entity, 0, -4);
        }

        return $this->render('administration/sql_interface.html.twig', [
            'entities' => $entities
        ]);
    }

    /**
     * @Route("/{_locale}/admin/manage_policy", name="administration_manage_policy", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function managePolicyAction(Request $request)
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.manage_policy');
        $license = \fopen(__dir__.'/../../LICENSE', 'r');
        $privacy_policy = \fopen(__dir__.'/../../public/policies/PRIVACY_POLICY.txt', 'r');
        $cookie_policy = \fopen(__dir__.'/../../public/policies/COOKIE_POLICY.txt', 'r');
        $code_of_conduct = \fopen(__dir__.'/../../public/policies/CODE_OF_CONDUCT.txt', 'r');

        if (filesize(__dir__.'/../../LICENSE')) {
            $license = \fread($license, filesize(__dir__.'/../../LICENSE'));
        } else {
            $license = "";
        } if (filesize(__dir__.'/../../public/policies/PRIVACY_POLICY.txt')) {
            $privacy_policy = \fread($privacy_policy, filesize(__dir__.'/../../public/policies/PRIVACY_POLICY.txt'));
        } else {
            $privacy_policy = "";
        } if (filesize(__dir__.'/../../public/policies/COOKIE_POLICY.txt')) {
            $cookie_policy = \fread($cookie_policy, filesize(__dir__.'/../../public/policies/COOKIE_POLICY.txt'));
        } else {
            $cookie_policy = "";
        } if (filesize(__dir__.'/../../public/policies/CODE_OF_CONDUCT.txt')) {
            $code_of_conduct = \fread($code_of_conduct, filesize(__dir__.'/../../public/policies/CODE_OF_CONDUCT.txt'));
        } else {
            $code_of_conduct = "";
        }

        $datas = array(
            'license' => $license,
            'privacy_policy' => $privacy_policy,
            'cookie_policy' => $cookie_policy,
            'code_of_conduct' => $code_of_conduct
        );

        $form = $this->container->get('form.factory')->createNamedBuilder('manage_policy', FormType::class, $datas, ['action' => $this->generateUrl('administration_manage_policy')])
            ->add('license', TextareaType::class, array('required' => false))
            ->add('privacy_policy', TextareaType::class, array('required' => false))
            ->add('cookie_policy', TextareaType::class, array('required' => false))
            ->add('code_of_conduct', TextareaType::class, array('required' => false))
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();

            if ($datas['license'] != $license) {
                $license = \fopen(__dir__.'/../../LICENSE', 'w');
                \fwrite($license, $datas['license']);
            } if ($datas['privacy_policy'] != $privacy_policy) {
                $privacy_policy = \fopen(__dir__.'/../../public/policies/PRIVACY_POLICY.txt', 'w');
                \fwrite($privacy_policy, $datas['privacy_policy']);
            } if ($datas['cookie_policy'] != $cookie_policy) {
                $cookie_policy = \fopen(__dir__.'/../../public/policies/COOKIE_POLICY.txt', 'w');
                \fwrite($cookie_policy, $datas['cookie_policy']);
            } if ($datas['code_of_conduct'] != $code_of_conduct) {
                $code_of_conduct = \fopen(__dir__.'/../../public/policies/CODE_OF_CONDUCT.txt', 'w');
                \fwrite($code_of_conduct, $datas['code_of_conduct']);
            }

            return $this->redirectToRoute('administration');
        }

        return $this->render('administration/manage_policy.html.twig', array(
            'manage_policy' => $form->createView()
        ));
    }

    /**
     * @Route("/{_locale}/admin/sql/{entity}/{max}/{id}", name="administration_sql", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function sqlAction(Request $request, string $entity, int $max = 10, ?string $id = NULL)
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.sql_interface');
        if ($entity == "user") {
            $repo = $this->getDoctrine()->getRepository(User::class);
        } elseif ($entity == "post") {
            $repo = $this->getDoctrine()->getRepository(Post::class);
        } elseif ($entity == "comment") {
            $repo = $this->getDoctrine()->getRepository(Comment::class);
        } elseif ($entity == "notification") {
            $repo = $this->getDoctrine()->getRepository(Notification::class);
        } elseif ($entity == "friendrequest") {
            $repo = $this->getDoctrine()->getRepository(FriendRequest::class);
        } elseif ($entity == "statistics") {
            $repo = $this->getDoctrine()->getRepository(Statistics::class);
        } elseif ($entity == "laws") {
            $repo = $this->getDoctrine()->getRepository(Laws::class);
        } elseif ($entity == "report") {
            $repo = $this->getDoctrine()->getRepository(Report::class);
        } elseif ($entity == "survey") {
            $repo = $this->getDoctrine()->getRepository(Survey::class);
        } else {
            throw new \Exception('Sorry, but this entity doesn\'t exist.');
        }

        $getArray = false;

        if ($request->isMethod('GET')) {
            $criteria = new Criteria;

            if (empty($id)) {
                $criteria->where($criteria->expr()->gte('id', 1));
                $criteria->setMaxResults($max);
            } else {
                $criteria->where($criteria->expr()->eq('id', $id));
            }

            $entities = $repo->matching($criteria);
        } else if ($request->isMethod('POST')) {
            $query = \json_decode($_POST["q"], true);

            $select = '';
            foreach ($query["select"] as $value) {
                $select .= 't.' . $value . ', ';
                $getArray = true;
                if ($value == "*") {
                    $select = substr($select, 0, -5) . 't, ';
                }
            }
            $select = substr($select, 0, -2);

            if ($select == "t") {
                $getArray = false;
            }

            $where = '';
            if ($query["where"]) {
                foreach ($query["where"] as $value) {
                    $where .= 't.' . $value["column"] . ' ' . $value["comparison"] . ' ' . $value["data"] . ' OR ';
                }
                $where = substr($where, 0, -4);
            }

            if ($where) {
                if (isset($query["orderby"]["order"])) {
                    $entities = $repo->createQueryBuilder('t')
                        ->select($select)
                        ->where($where)
                        ->orderBy('t.' . $query["orderby"]["column"], $query["orderby"]["order"])
                        ->setMaxResults($query["limit"])
                        ->getQuery()
                        ->getResult();
                } else {
                    $entities = $repo->createQueryBuilder('t')
                        ->select($select)
                        ->where($where)
                        ->setMaxResults($query["limit"])
                        ->getQuery()
                        ->getResult();
                }
            } else {
                if (isset($query["orderby"]["order"])) {
                    $entities = $repo->createQueryBuilder('t')
                        ->select($select)
                        ->orderBy('t.' . $query["orderby"]["column"], $query["orderby"]["order"])
                        ->setMaxResults($query["limit"])
                        ->getQuery()
                        ->getResult();
                } else {
                    $entities = $repo->createQueryBuilder('t')
                        ->select($select)
                        ->setMaxResults($query["limit"])
                        ->getQuery()
                        ->getResult();
                }
            }
        }

        $response = 'The query returned no entity.';

        if (isset($entities[0])) {
            $response = '<table>';
            // Table Head
            $response .= '<tr>';
            if (!$getArray) {
                $browsed = $entities[0]->browse();
            } else {
                $browsed = $entities[0];
            }
            foreach ($browsed as $entitykey => $entityvalue) {
                $response .= '<th>' . $entitykey . '</th>';
            }
            $response .= '</tr>';
            // Entities
            foreach ($entities as $entityvalue) {
                $response .= '<tr>';
                if (!$getArray) {
                    $browsedEntity = $entityvalue->browse();
                } else {
                    $browsedEntity = $entityvalue;
                }
                foreach ($browsedEntity as $value) {
                    if ($value instanceof \DateTime) {
                        $response .= '<td><div>' . \date_format($value, 'Y-m-d') . '</div></td>';
                    } elseif ($value instanceof \Doctrine\ORM\PersistentCollection || is_array($value)) {
                        $notempty = false;
                        $response .= '<td><div>[';
                        foreach ($value as $subvalue) {
                            $response .= '\'' . $subvalue . '\', ';
                            $notempty = true;
                        }
                        if ($notempty) {
                            $response = substr($response, 0, -2);
                            $response .= ']</div></td>';
                        } else {
                            $response = substr($response, 0, -1);
                            $response .= 'NULL</div></td>';
                        }
                    } else {
                        $response .= '<td><div>' . $value . '</div></td>';
                    }
                }
                $response .= '</tr>';
            }
            $response .= '</table>';
        }

        return new Response($response);
    }

    /**
     * @Route("/{_locale}/admin/get_sql_entity_columns/{entity}", name="administration_get_sql_entity_columns", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function getSQLEntityColumnsAction(string $entity)
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.sql_interface');
        header('Content-Type: text/json');

        if ($entity == "user") {
            $repo = $this->getDoctrine()->getRepository(User::class);
        } elseif ($entity == "post") {
            $repo = $this->getDoctrine()->getRepository(Post::class);
        } elseif ($entity == "comment") {
            $repo = $this->getDoctrine()->getRepository(Comment::class);
        } elseif ($entity == "notification") {
            $repo = $this->getDoctrine()->getRepository(Notification::class);
        } elseif ($entity == "friendrequest") {
            $repo = $this->getDoctrine()->getRepository(FriendRequest::class);
        } elseif ($entity == "statistics") {
            $repo = $this->getDoctrine()->getRepository(Statistics::class);
        } elseif ($entity == "laws") {
            $repo = $this->getDoctrine()->getRepository(Laws::class);
        } elseif ($entity == "survey") {
            $repo = $this->getDoctrine()->getRepository(Survey::class);
        } else {
            throw new \Exception('Sorry, but this entity doesn\'t exist.');
        }

        $browsed = $repo->findOneBy([])->browse();

        $columns = ['*'];

        foreach ($browsed as $column => $value) {
            $columns[] = $column;
        }

        return new Response(\json_encode($columns), 200, array('Content-Type' => 'text/json'));
    }

    /**
     * @Route("/{_locale}/admin/get_roles/{user}", name="administration_get_roles", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function getUserRolesAction(User $user)
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.manage_roles');
        header('Content-Type: text/json');

        $array = array(
            'name' => $user->getFirstName()." ".$user->getLastName(),
            'username' => $user->getUsername(),
            'img' => $user->getUrl(),
            'nbPosts' => \count($user->getPosts()),
            'nbComments' => \count($user->getComments()),
            'nbFriends' => \count($user->getFriends()),
            'dateSignUp' => \date_format($user->getDateSign(), 'Y-m-d'),
            'roles' => $user->getRoles()
        );

        return new Response(\json_encode($array), 200, array('Content-Type' => 'text/json'));
    }

    /**
     * @Route("/{_locale}/admin/manage_roles/{new_role}/{user}", name="administration_new_role", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function manageUserRolesAction(string $new_role, User $user)
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.manage_roles');
        // We verify that what the user want to do isn't illegal.
        if (\strtolower($new_role) == "admin" || $user->getRoles()[0] == "ROLE_ADMIN") {
            $this->denyAccessUnlessGranted('ROLE_ROOT');
        } else if (\strtolower($new_role) == "root" || $user->getRoles()[0] == "ROLE_ROOT") {
            $this->denyAccessUnlessGranted('ROLE_OWNER');
        } else if ($user->getRoles()[0] == "ROLE_OWNER") {
            throw new AccessDeniedHttpException('Roles of ROLE_OWNER users CAN\'T be modified!');
        } else if (\strtolower($new_role) == "owner") {
            throw new AccessDeniedHttpException('You CAN\'T assign ROLE_OWNER to someone!');
        }

        if (!(\strtolower($new_role) == "user" || \strtolower($new_role) == "helper" || \strtolower($new_role) == "dev" || \strtolower($new_role) == "design" || \strtolower($new_role) == "trans" || \strtolower($new_role) == "mod" || \strtolower($new_role) == "admin" || \strtolower($new_role) == "root")) {
            throw new NotFoundHttpException('Ehh, this role doesn\'t exist!');
        }

        // Converting root to ROLE_ROOT for example.
        // We didn't pass ROLE_ROOT directly to router, for ergonomic reasons (it's easier to write).
        $new_role = "ROLE_" . \strtoupper($new_role);

        $user->setRoles(array($new_role));

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new Response('true');
    }

    /**
     * @Route("/{_locale}/admin/manage_authorizations", name="administration_manage_authorizations", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function manageAuthorizationsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('administration.access');
        $this->denyAccessUnlessGranted('administration.accessible.manage_authorizations');
        $yaml = $this->getParameter("authorizations");
        $roles = [];
        foreach ($this->getParameter("roles")['list'] as $role) {
            if ($this->isGranted($role) || !\in_array($role, $this->getUser()->getRoles())) {
                $roles[$role] = $role;
            }
        }
        $roles = array_merge(['ALL' => 'ALL'], $roles, ['NONE' => 'NONE']);
        $datas = [];
        foreach ($yaml as $category => $authorizations) {
            foreach ($authorizations as $slug => $value) {
                if (gettype($value) != 'array') {
                    $datas[$category.':'.$slug] = $value;
                } else {
                    foreach ($value as $subslug => $subvalue) {
                        $datas[$category.':'.$slug.':'.$subslug] = $subvalue;
                    }
                }
            }
        }
        $form = $this->container->get('form.factory')->createNamedBuilder('manage_authorizations', FormType::class, $datas, ['action' => $this->generateUrl('administration_manage_authorizations')]);
        foreach ($yaml as $category => $authorizations) {
            foreach ($authorizations as $slug => $value) {
                if (gettype($value) != 'array') {
                    $form = $form->add($category.':'.$slug, ChoiceType::class, ['choices' => $roles]);
                } else {
                    foreach ($value as $subslug => $subvalue) {
                        $form = $form->add($category.':'.$slug.':'.$subslug, ChoiceType::class, ['choices' => $roles]);
                    }
                }
            }
        }
        $form->add('submit', SubmitType::class);
        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();

            foreach ($datas as $key => $data) {
                $exploded_key = \explode(':', $key);
                if (\sizeof($exploded_key) == 2) {
                    $yaml[$exploded_key[0]][$exploded_key[1]] = $data;
                } elseif (\sizeof($exploded_key) == 3) {
                    $yaml[$exploded_key[0]][$exploded_key[1]][$exploded_key[2]] = $data;
                }
            }
            
            $file_content = Yaml::parseFile(__dir__.'/../../config/config.yaml');
            $file_content["parameters"]["authorizations"] = $yaml;
            $file_content = Yaml::dump($file_content, 99, 4);
            file_put_contents(__dir__.'/../../config/config.yaml', $file_content);
            
            return $this->redirectToRoute('administration');
        }

        return $this->render('administration/manage_authorizations.html.twig', [
            'manage_authorizations' => $form->createView(),
            'categories' => array_keys($yaml),
            'keys' => array_keys($datas)
        ]);
    }
}
