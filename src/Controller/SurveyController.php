<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Survey;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SurveyController extends Controller
{
    /**
     * Create a new survey
     *
     * @Route("/{_locale}/survey", name="survey", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function surveyAction()
    {
        $survey = new Survey;
        $survey->setUser($this->getUser());
        $survey->setQuestion("Une petite question");
        $survey->addAnswerOption("Yes", "00FF00");
        $survey->addAnswerOption("No", "FF0000");
        $survey->addAnswerOption("I Don't Know", "CCCCCC");

        $em = $this->getDoctrine()->getManager();
        $em->persist($survey);
        $em->flush();

        return $this->redirectToRoute('survey_show', array(
            'survey' => $survey->getId()
        ));
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
