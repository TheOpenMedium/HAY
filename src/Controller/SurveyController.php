<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Survey;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SurveyController extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Checking if a new survey have been created inside a post
     */
    public function surveyCheckPostAction(Post $post)
    {
        preg_match_all("/\[survey(?:(?: role=['\"](\w*)['\"])| (anonymous))*\]\s+((?:#|\-|\:|\"|\'|\,|\.|\;|\?|\!|\w|\s)*)\s+\[\/survey\]/iu", $post->getContent(), $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (!empty($match[3])) {
                preg_match_all("/(?:(?:# ((?:\w| |\#|\-|\:|\"|\'|\,|\.|\;|\?|\!)+))|(?:-(?:\:['\"](\w+)['\"])? ((?:\w| |\#|\-|\:|\"|\'|\,|\.|\;|\?|\!)+)))/ium", $match[3], $match[3], PREG_SET_ORDER);
                if (!empty($match[3])) {
                    $survey = new Survey;
                    $survey->setUser($this->getUser());
                    foreach ($match[3] as $submatch) {
                        if ($submatch[1]) {
                            $survey->setQuestion($submatch[1]);
                        } elseif ($submatch[3]) {
                            if ($submatch[2]) {
                                $survey->addAnswerOption($submatch[3], $submatch[2]);
                            } else {
                                $survey->addAnswerOption($submatch[3], dechex(rand(0,10000000)));
                            }
                        }
                    }

                    // TODO: Create anonymous and non-anonymous surveys.
                    // TODO: Adding a security to role param.
                    if ($match[1]) {
                        $survey->setRole(strtoupper($match[1]));
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($survey);
                    $em->flush();
                    $post->setContent(preg_replace("/\[survey(?:(?: role=['\"](\w*)['\"])| (anonymous))*\]\s+((?:#|\-|\:|\"|\'|\,|\.|\;|\?|\!|\w|\s)*)\s+\[\/survey\]/iu", "[survey " . $survey->getId() . " /]", $post->getContent(), 1));
                } else {
                    $post->setContent(preg_replace("/\[survey(?:(?: role=['\"](\w*)['\"])| (anonymous))*\]\s+((?:#|\-|\:|\"|\'|\,|\.|\;|\?|\!|\w|\s)*)\s+\[\/survey\]/iu", "[PARSE ERROR: NEITHER QUESTION NOR ANSWER HAVE BEEN FOUND IN SURVEY]", $post->getContent(), 1));
                }
            } else {
                $post->setContent(preg_replace("/\[survey(?:(?: role=['\"](\w*)['\"])| (anonymous))*\]\s+((?:#|\-|\:|\"|\'|\,|\.|\;|\?|\!|\w|\s)*)\s+\[\/survey\]/iu", "[PARSE ERROR: SURVEY IS EMPTY]", $post->getContent(), 1));
            }
        }
        return $post;
    }

    /**
     * Checking if a new survey have been created inside a comment
     */
    public function surveyCheckCommentAction(Comment $comment)
    {
        //
    }

    /**
     * Render a single survey
     *
     * @param Survey $survey The survey to render
     *
     * @Route("/{_locale}/show/survey/{survey}", name="survey_show", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function showSurveyAction(Survey $survey)
    {
        return $this->render('survey/showSurvey.html.twig', array(
            "survey" => $survey
        ));
    }

    /**
     * Render HTML a single survey
     *
     * @param Survey $survey The survey to render
     *
     * @Route("/{_locale}/display/survey/{survey}", name="survey_display", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function displaySurveyAction(Survey $survey)
    {
        return $this->render('survey/survey.html.twig', array(
            "survey" => $survey
        ));
    }

    /**
     * Answer to a survey
     *
     * @param Survey $survey The survey
     *
     * @Route("/{_locale}/vote/survey/{survey}", name="survey_vote", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function voteSurveyAction(Request $request, Survey $survey)
    {
        if (!$request->isMethod('POST') || !array_key_exists("answer", $_POST)) {
            return new Response("false");
        }

        $survey->addAnswer($_POST['answer'], $this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new Response("true");
    }

    /**
     * Delete a survey from the database
     *
     * @param Survey $survey The survey to delete
     *
     * @Route("/{_locale}/delete/survey/{survey}", name="survey_delete", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function deleteSurveyAction(Survey $survey)
    {
        $user = $this->getUser();

        // And delete it if the user and the author are the same.
        if ($survey->getUser()->getId() == $user->getId()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($survey);
            $entityManager->flush();
        }

        // Finally the user is redirected to home page.
        return $this->redirectToRoute('app_home');
    }
}
