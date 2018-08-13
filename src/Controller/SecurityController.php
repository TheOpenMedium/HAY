<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\FriendRequest;
use App\Entity\Statistics;
use App\Entity\Laws;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Yaml\Yaml;

class SecurityController extends Controller
{
    /**
     * @Route("/{_locale}/root", name="security_root", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function rootAction(Request $request)
    {
        $yaml = Yaml::parseFile(__dir__.'/../../config/packages/twig.yaml');

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
            'version' => $yaml['twig']['globals']['is_version_displayed'],
            'license' => $license,
            'privacy_policy' => $privacy_policy,
            'cookie_policy' => $cookie_policy,
            'code_of_conduct' => $code_of_conduct
        );

        $form = $this->createFormBuilder($datas)
            ->add('HAYlogo', FileType::class, array('required' => false))
            ->add('icon', FileType::class, array('required' => false))
            ->add('version', CheckboxType::class, array('required' => false))
            ->add('license', TextareaType::class, array('required' => false))
            ->add('privacy_policy', TextareaType::class, array('required' => false))
            ->add('cookie_policy', TextareaType::class, array('required' => false))
            ->add('code_of_conduct', TextareaType::class, array('required' => false))
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

            $yaml['twig']['globals']['is_version_displayed'] = $datas['version'];
            $fp = \fopen(__dir__.'/../../config/packages/twig.yaml', 'w');
            \fwrite($fp, Yaml::dump($yaml));
        }

        return $this->render('security/root.html.twig', array(
            'form' => $form->createView(),
            'entities' => $entities
        ));
    }

    /**
     * @Route("/{_locale}/root/sql/{entity}/{max}/{first}", name="security_root_sql", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function sqlAction(string $entity, int $max = 10, int $first = 1)
    {
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
        } else {
            throw new \Exception('Sorry, but this entity doesn\'t exist.');
        }

        $criteria = new Criteria;
        $criteria->where($criteria->expr()->gte('id', $first));
        $criteria->setMaxResults($max);

        $entities = $repo->matching($criteria);

        $response = 'There is no entity.';

        if ($entities) {
            $response = '<table>';
            // Table Head
            $response .= '<tr>';
            foreach ($entities[0]->browse() as $entitykey => $entityvalue) {
                $response .= '<th>' . $entitykey . '</th>';
            }
            $response .= '</tr>';
            // Entities
            foreach ($entities as $entityvalue) {
                $response .= '<tr>';
                    foreach ($entityvalue->browse() as $value) {
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
     * @Route("/{_locale}/root/get_sql_entity_columns/{entity}", name="security_root_get_sql_entity_columns", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
     public function getSQLEntityColumnsAction(string $entity)
     {
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
     * @Route("/{_locale}/admin", name="security_admin", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function adminAction()
    {
        return $this->render('security/admin.html.twig');
    }

    /**
     * @Route("/{_locale}/admin/get_roles/{user}", name="security_admin_get_roles", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function getUserRolesAction(User $user)
    {
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
     * @Route("/{_locale}/admin/manage_roles/{new_role}/{user}", name="security_admin_manage_roles", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function manageUserRolesAction(string $new_role, User $user)
    {
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
     * @Route("/{_locale}/mod", name="security_mod", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function modAction()
    {
        return $this->render('security/mod.html.twig');
    }

    /**
     * @Route("/{_locale}/trans", name="security_trans", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function transAction()
    {
        return $this->render('security/trans.html.twig');
    }

    /**
     * @Route("/{_locale}/design", name="security_design", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function designAction()
    {
        return $this->render('security/design.html.twig');
    }

    /**
     * @Route("/{_locale}/dev", name="security_dev", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function devAction()
    {
        return $this->render('security/dev.html.twig');
    }

    /**
     * @Route("/{_locale}/helper", name="security_helper", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function helperAction()
    {
        return $this->render('security/helper.html.twig');
    }
}
